<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\GoldRate;
use Carbon\Carbon;

class GoldRateController extends Controller
{
    public function showTodayRate()
    {
        $today = Carbon::today();
        $goldRate = GoldRate::whereDate('date', $today)->where('type', 1)->value('price');

        return view('home', compact('goldRate'));
    }

    // Optional: Store a new gold rate
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|integer',
            'price' => 'required|numeric',
            'date' => 'required|date',
            'created_by' => 'required|integer',
        ]);

        GoldRate::create($request->only('type', 'price', 'date', 'created_by'));

        return redirect()->back()->with('success', 'Gold rate added successfully.');
    }
}