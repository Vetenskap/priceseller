<?php

namespace Modules\Assembly\Http\Controllers;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\HttpClient\OzonClient\Resources\FBS\CarriageAvailableList;
use App\Models\OzonMarket;
use Illuminate\Http\Request;

class AssembltOzonBarcodesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Helpers::user();

        $barcodes = $user->ozonMarkets->map(function (OzonMarket $market) {
            $result = collect();
            $carriages = CarriageAvailableList::getAll($market->api_key, $market->client_id);
            $result = $result->put('carriages', $carriages->filter(fn (CarriageAvailableList $carriage) => $carriage->getCarriageId() && $carriage->getCarriagePostingsCount())->each(fn (CarriageAvailableList $carriage) => $carriage->fetchActBarcode($market->api_key, $market->client_id)));
            $result = $result->put('market', $market);
            return $result;
        });

        return view('assembly::assembly-ozon-barcodes', compact('barcodes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('assembly::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('assembly::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('assembly::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
