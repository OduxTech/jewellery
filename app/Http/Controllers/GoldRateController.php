<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\GoldRate;
use App\Utils\ModuleUtil;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use App\Brands;
use App\Product;
use App\Variation;
use App\Utils\ProductUtils;
use App\VariationGroupPrice;
use App\SellingPriceGroup;
use App\Utils\Util;
use DB;
use Spatie\Permission\Models\Permission;





class GoldRateController extends Controller
{

    
    /**
     * All Utils instance.
     */
    protected $commonUtil;

    /**
     * Constructor
     *
     * @param  ProductUtils  $product
     * @return void
     */
   public function __construct(Util $commonUtil)
    {
        $this->commonUtil = $commonUtil;
    }

    public function showTodayRate()
    {
        $types = [1, 2, 3];
        $latestRates = [];

        foreach ($types as $type) {
            $latestRates[$type] = GoldRate::where('type', $type)
                ->orderBy('id', 'desc')
                ->first();
        }

        return view('home', compact('latestRates'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_old(Request $request)
    {
        if (!auth()->user()->can('brand.create')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'gold_price' => 'required|numeric|min:0',
            'silver_price' => 'required|numeric|min:0',
        ]);

        try {
            $business_id = $request->session()->get('user.business_id');
            $created_by = $request->session()->get('user.id');
            $date = Carbon::today();

            // Create 24K Gold entry (type = 1)
            $gold = GoldRate::create([
                'type' => 1,
                'price' => $request->input('gold_price'),
                'date' => $date,
                'created_by' => $created_by,
            ]);

            // Create Silver entry (type = 3)
            $silver = GoldRate::create([
                'type' => 3,
                'price' => $request->input('silver_price'),
                'date' => $date,
                'created_by' => $created_by,
            ]);

            $output = [
                'success' => true,
                'data' => [
                    'gold' => $gold,
                    'silver' => $silver
                ],
                'msg' => __('brand.updated_gold_success'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . ' Line:' . $e->getLine() . ' Message:' . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('brand.create')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'gold_price' => 'required|numeric|min:0',
            'silver_price' => 'required|numeric|min:0',
        ]);

        try {
            $business_id = $request->session()->get('user.business_id');
            $created_by = $request->session()->get('user.id');
            $date = \Carbon\Carbon::today();

            // Save new rates
            $gold = GoldRate::create([
                'type' => 1,
                'price' => $request->input('gold_price'),
                'date' => $date,
                'created_by' => $created_by,
            ]);

            $silver = GoldRate::create([
                'type' => 3,
                'price' => $request->input('silver_price'),
                'date' => $date,
                'created_by' => $created_by,
            ]);

            // --- START: Update product prices based on new gold price ---
            $gold_price = $request->input('gold_price');
            $gold_price = round($gold_price / 8, 2);

            $margin = 5; // Set your margin here, e.g. 0 for no margin

            //$gold_price = ($gold_price / 24)  ; // Convert to per gram price


            // Get all 24K gold products (brand_id = 24)
            $gold_products = Product::with('variations', 'product_tax')
                ->where('business_id', $business_id)
                ->where('brand_id', 1)
                ->get();

            foreach ($gold_products as $product) {
                $tax_percent = optional($product->product_tax()->first())->amount ?? 0;
                $cost_percent = $product->cost_percent ?? 0;

                foreach ($product->variations as $variation) {
                    // Get weight in grams from variation name
                    $grams = floatval($variation->name); // e.g. "1", "2", etc.
                    $making_charge = $variation->making_charge ?? 0;

                    // Final price = gold_rate * grams + making charge
                    $base_price = ($gold_price * $grams) * ($cost_percent/100);
                    $base_price = $base_price * ($margin/100) + $base_price ;

                    $variation->sell_price_inc_tax = $base_price;
                    $variation->default_sell_price =  $base_price;
                    $variation->profit_percent = $this->commonUtil->get_percent(
                        $variation->default_purchase_price,
                        $variation->default_sell_price
                    );
                    $variation->update();
                }
            }

            // --- END: Update logic ---

            $output = [
                'success' => true,
                'data' => [
                    'gold' => $gold,
                    'silver' => $silver
                ],
                'msg' => __('brand.updated_gold_success'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . ' Line:' . $e->getLine() . ' Message:' . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }




    public function create()
    {
        if (!auth()->user()->can('brand.create')) {
            abort(403, 'Unauthorized action.');
        }

        $quick_add = false;
        if (!empty(request()->input('quick_add'))) {
            $quick_add = true;
        }

        $types = [1, 2, 3];
        $latestRates = [];

        foreach ($types as $type) {
            $latestRates[$type] = GoldRate::where('type', $type)
                ->orderBy('id', 'desc')
                ->first();
        }

        //$is_repair_installed = $this->moduleUtil->isModuleInstalled('Repair');

        return view('home.partials.gold_rate_update')
            ->with(compact('quick_add', 'latestRates'));
    }

    public function updateAllPrices(Request $request)
    {
        try {
            $notAllowed = $this->commonUtil->notAllowedInDemo();
            if (!empty($notAllowed)) {
                return $notAllowed;
            }

            ini_set('max_execution_time', 0);
            ini_set('memory_limit', -1);

            $business_id = $request->user()->business_id;
            $price_groups = SellingPriceGroup::where('business_id', $business_id)->active()->get();

            $variations = Variation::with('product')->whereHas('product', function ($q) use ($business_id) {
                $q->where('business_id', $business_id);
            })->get();

            DB::beginTransaction();

            foreach ($variations as $variation) {
                // EXAMPLE: set new price based on purchase price + fixed markup (e.g., 30%)
                $purchase_price = $variation->default_purchase_price ?? 0;
                $new_price = $purchase_price * 1.3;

                $tax_percent = optional($variation->product->product_tax()->first())->amount ?? 0;
                $default_sell_price = $this->commonUtil->calc_percentage_base($new_price, $tax_percent);

                $variation->sell_price_inc_tax = $new_price;
                $variation->default_sell_price = $default_sell_price;
                $variation->profit_percent = $this->commonUtil->get_percent($purchase_price, $default_sell_price);
                $variation->update();

                // Update all price groups with same price (you can customize this logic)
                foreach ($price_groups as $pg) {
                    VariationGroupPrice::updateOrCreate(
                        ['variation_id' => $variation->id, 'price_group_id' => $pg->id],
                        ['price_inc_tax' => $new_price]
                    );
                }
            }

            DB::commit();

            $output = [
                'success' => 1,
                'msg' => __('lang_v1.product_prices_updated_successfully'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:' . $e->getFile() . ' Line:' . $e->getLine() . ' Message:' . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => $e->getMessage(),
            ];
        }

        return redirect('update-product-price')->with('status', $output);
    }


}