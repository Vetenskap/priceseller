<?php

namespace Modules\Assembly\Http\Controllers;

use App\Http\Controllers\Controller;
use App\HttpClient\WbClient\Resources\Order;
use App\HttpClient\WbClient\Resources\Sticker;
use App\HttpClient\WbClient\Resources\Supply;
use App\Models\Item;
use Illuminate\Http\Request;
use Modules\Assembly\Models\AssemblyWbSupply;

class AssemblyWbSupplyStickersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(AssemblyWbSupply $supply)
    {
        $wbSupply = new Supply();
        $wbSupply->setId($supply->id_supply);
        $wbSupply->fetchOrders($supply->market->api_key);

        $ordersIds = $wbSupply->getOrders()->map(fn (Order $order) => $order->getId())->toArray();
        $stickers = Sticker::getFromOrderIds($ordersIds, $supply->market->api_key, 'svg');

        $orders = $wbSupply->getOrders()->map(function (Order $order) use ($stickers) {
            $order->setSticker($stickers->firstWhere(fn (Sticker $sticker) => $sticker->getOrderId() === $order->getId()));
            return $order;
        });

        $orders = $orders->sort(function (Order $a, Order $b) use ($supply) {
            // 1. Сортировка по наличию остатка (stock > 0 первыми)
            $stockA = $this->getStock($a);
            $stockB = $this->getStock($b);

            if (($stockA > 0) && ($stockB <= 0)) {
                return -1; // $a идет выше $b
            }

            if (($stockA <= 0) && ($stockB > 0)) {
                return 1; // $b идет выше $a
            }

            // 2. Сортировка по дате создания (от старых к новым)
            $dateA = $a->getCreatedAt($supply->market->user);
            $dateB = $b->getCreatedAt($supply->market->user);

            return strtotime($dateA) <=> strtotime($dateB);
        });

        return view('assembly::assembly-wb-supply-stickers', compact('orders'));
    }

    private function getStock(Order $order): int
    {
        if ($order->getCard()->getProduct()?->itemable instanceof Item) {
            return $order->getCard()->getProduct()?->itemable->warehousesStocks()->sum('stock') ?? 0;
        }

        return $order->getCard()->getProduct()?->itemable->items
            ->sortBy(fn(Item $item) => $item->warehousesStocks()->sum('stock'))
            ->first()?->warehousesStocks()->sum('stock') ?? 0;
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
