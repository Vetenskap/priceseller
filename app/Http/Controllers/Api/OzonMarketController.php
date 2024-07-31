<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OzonMarket;
use Illuminate\Http\Request;

class OzonMarketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return response()->json(['markets' => $request->user()->ozonMarkets]);
    }

    public function getItem(Request $request, string $market)
    {
        $market = $request->user()->ozonMarkets()->findOrFail($market);
        $offerId = $request->input('offer_id');

        return response()->json(['item' => $market->items()->where('offer_id', $offerId)->with('item')->first()]);
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
