<?php

namespace Modules\Order\Exports;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Modules\Order\Models\OrderItemWriteOffItemWarehouseStock;
use Modules\Order\Models\WriteOffItemWarehouseStock;

class WriteOffItemWarehouseStockExport implements FromCollection, WithHeadings
{
    public function __construct(public string $organizationId){}


    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $items = OrderItemWriteOffItemWarehouseStock::with(['itemWarehouseStock', 'orderItem.order'])->whereHas('orderItem', function (Builder $builder) {
            $builder->whereHas('order', function (Builder $builder) {
                $builder->where('organization_id', $this->organizationId);
            });
        })->get();

        return $items->map(function (OrderItemWriteOffItemWarehouseStock $stock) {
            return [
                'warehouse_name' => $stock->itemWarehouseStock->warehouse->name,
                'order_number' => $stock->orderItem->order->number,
                'stock' => $stock->stock,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Склад',
            'Номер заказа',
            'Количество',
        ];
    }
}
