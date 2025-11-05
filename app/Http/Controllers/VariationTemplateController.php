<?php

namespace App\Http\Controllers;

use App\ProductVariation;
use App\Variation;
use App\VariationTemplate;
use App\VariationValueTemplate;
use DB;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class VariationTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $variations = VariationTemplate::where('business_id', $business_id)
                        ->with(['values'])
                        ->select('id', 'name', DB::raw('(SELECT COUNT(id) FROM product_variations WHERE product_variations.variation_template_id=variation_templates.id) as total_pv'));

            return Datatables::of($variations)
                ->addColumn(
                    'action',
                    '<button data-href="{{action(\'App\Http\Controllers\VariationTemplateController@edit\', [$id])}}" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-primary edit_variation_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                        &nbsp;
                        @if(empty($total_pv))
                        <button data-href="{{action(\'App\Http\Controllers\VariationTemplateController@destroy\', [$id])}}" class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-error delete_variation_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                        @endif'
                )
                ->editColumn('values', function ($data) {
                    $values_arr = [];
                    foreach ($data->values as $attr) {
                        $values_arr[] = $attr->name;
                    }

                    return implode(', ', $values_arr);
                })
                ->removeColumn('id')
                ->removeColumn('total_pv')
                ->rawColumns([2])
                ->make(false);
        }

        return view('variation.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('variation.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
   public function store(Request $request)
    {
        try {
            // Validate base input
            $request->validate([
                'name' => 'required|string|max:255',
                'variation_values' => 'required|array|min:1',
                'variation_values.*' => 'required|string|max:255'
            ]);

            $values = $request->input('variation_values');

            // Check for duplicate values
            if (count($values) !== count(array_unique(array_map('strtolower', $values)))) {
                return [
                    'success' => false,
                    'msg' => 'Duplicate variation values are not allowed.',
                ];
            }

            // Create variation template
            $input = $request->only(['name']);
            $input['business_id'] = $request->session()->get('user.business_id');
            $variation = VariationTemplate::create($input);

            // Create variation values
            $data = [];
            foreach ($values as $value) {
                if (!empty($value)) {
                    $data[] = ['name' => $value];
                }
            }
            $variation->values()->createMany($data);

            $output = [
                'success' => true,
                'data' => $variation,
                'msg' => 'Variation added successfully',
            ];
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return [
                'success' => false,
                'msg' => $ve->getMessage(),
                'errors' => $ve->errors()
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . ' Line:' . $e->getLine() . ' Message:' . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => 'Something went wrong, please try again',
            ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\VariationTemplate  $variationTemplate
     * @return \Illuminate\Http\Response
     */
    public function show(VariationTemplate $variationTemplate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $variation = VariationTemplate::where('business_id', $business_id)
                            ->with(['values'])->find($id);

            return view('variation.edit')
                ->with(compact('variation'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
{
    if ($request->ajax()) {
        try {
            $input = $request->only(['name']);
            $business_id = $request->session()->get('user.business_id');
            $variation = VariationTemplate::where('business_id', $business_id)->findOrFail($id);

            // Validate input
            $request->validate([
                'name' => 'required|string|max:255',
                'edit_variation_values' => 'nullable|array',
                'edit_variation_values.*' => 'required|string|max:255',
                'variation_values' => 'nullable|array',
                'variation_values.*' => 'required|string|max:255',
            ]);

            // Merge all values to check for duplicates (case-insensitive)
            $all_values = [];

            if ($request->filled('edit_variation_values')) {
                $all_values = array_merge($all_values, array_map('strtolower', $request->input('edit_variation_values')));
            }

            if ($request->filled('variation_values')) {
                $all_values = array_merge($all_values, array_map('strtolower', $request->input('variation_values')));
            }

            if (count($all_values) !== count(array_unique($all_values))) {
                return [
                    'success' => false,
                    'msg' => 'Duplicate variation values are not allowed.',
                ];
            }

            // Update name if changed
            if ($variation->name != $input['name']) {
                $variation->name = $input['name'];
                $variation->save();

                // Update related product variations
                ProductVariation::where('variation_template_id', $variation->id)
                    ->update(['name' => $variation->name]);
            }

            $dataToUpdate = [];
            if ($request->filled('edit_variation_values')) {
                foreach ($request->input('edit_variation_values') as $key => $value) {
                    if (!empty($value)) {
                        $variation_val = VariationValueTemplate::find($key);
                        if ($variation_val && $variation_val->name !== $value) {
                            $variation_val->name = $value;
                            $dataToUpdate[] = $variation_val;

                            Variation::where('variation_value_id', $key)
                                ->update(['name' => $value]);
                        }
                    }
                }
                $variation->values()->saveMany($dataToUpdate);
            }

            $dataToCreate = [];
            if ($request->filled('variation_values')) {
                foreach ($request->input('variation_values') as $value) {
                    if (!empty($value)) {
                        $dataToCreate[] = new VariationValueTemplate(['name' => $value]);
                    }
                }
                $variation->values()->saveMany($dataToCreate);
            }

            return [
                'success' => true,
                'msg' => 'Variation updated successfully',
            ];
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return [
                'success' => false,
                'msg' => 'Validation error occurred.',
                'errors' => $ve->errors()
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . ' Line:' . $e->getLine() . ' Message:' . $e->getMessage());

            return [
                'success' => false,
                'msg' => 'Something went wrong, please try again',
            ];
        }
    }
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

                $variation = VariationTemplate::where('business_id', $business_id)->findOrFail($id);
                $variation->delete();

                $output = ['success' => true,
                    'msg' => 'Category deleted succesfully',
                ];
            } catch (\Eexception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = ['success' => false,
                    'msg' => 'Something went wrong, please try again',
                ];
            }

            return $output;
        }
    }
}
