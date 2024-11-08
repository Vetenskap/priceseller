<?php

namespace Modules\Order\Exports;

use App\Models\Bundle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Modules\Order\Models\Order;

class SupplierOrderExport implements FromCollection, WithHeadings
{
    public string $organizationId;
    public string $supplierId;

    public function __construct(string $organizationId, string $supplierId)
    {
        $this->organizationId = $organizationId;
        $this->supplierId = $supplierId;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        /** @var Collection $orders */
        $orders = Order::whereHas('orderable')
            ->with('orderable.itemable')
            ->where('count', '>', 0)
            ->where('state', 'new')
            ->where('organization_id', $this->organizationId)
            ->get();

        $orders = $orders->map(function (Order $order) {
            if ($order->orderable->itemable instanceof Bundle) {
                $items = collect();

                foreach ($order->orderable->itemable->items as $item) {
                    if ($item->supplier_id === $this->supplierId) {
                        $items->push($item);
                    }
                }

                $order->orderable->itemable->items = $items;

                return $order;
            }

            if ($order->orderable->itemable->supplier_id === $this->supplierId) return $order;

            return null;

        })->filter()->values();

        return $orders->flatMap(function (Order $order) {
            if ($order->orderable->itemable instanceof Bundle) {
                return $order->orderable->itemable->items->map(function ($item) use ($order) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'code' => $item->code,
                        'article' => $item->article,
                        'brand' => $item->brand,
                        'count' => $order->count * $item->pivot->multiplicity,
                        'price' => $order->price,
                    ];
                });
            }

            return [[
                'id' => $order->orderable->itemable->id,
                'name' => $order->orderable->itemable->name,
                'code' => $order->orderable->itemable->code,
                'article' => $order->orderable->itemable->article,
                'brand' => $order->orderable->itemable->brand,
                'count' => $order->count * $order->orderable->itemable->multiplicity,
                'price' => $order->price,
            ]];
        })->groupBy('id')
            ->map(function ($items, $id) {

                $firstItem = $items->first();

                return [
                    'name' => $firstItem['name'],
                    'code' => $firstItem['code'],
                    'article' => $firstItem['article'],
                    'brand' => $firstItem['brand'],
                    'count' => $items->sum('count'),
                    'price' => $firstItem['price'],
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Наименование',
            'Код клиента',
            'Артикул',
            'Бренд',
            'Количество',
            'Цена',
        ];
    }
}
