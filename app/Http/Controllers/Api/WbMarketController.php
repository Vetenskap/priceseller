<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WbMarket;
use Illuminate\Http\Request;

class WbMarketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return response()->json(['markets' => $request->user()->wbMarkets]);
    }

    public function getItem(Request $request, string $market)
    {
        $market = $request->user()->wbMarkets()->findOrFail($market);
        $vendorCode = $request->input('vendor_code');

        return response()->json(['item' => $market->items()->where('vendor_code', $vendorCode)->with('item')->first()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
