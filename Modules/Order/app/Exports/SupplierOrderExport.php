<?php

namespace Modules\Order\Exports;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Modules\Order\Models\OrderItem;

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
        $ordersItems = OrderItem::with(['order', 'item'])
            ->where('count', '>', 0)
            ->whereHas('order', function (Builder $query) {
                $query
                    ->where('state', 'new')
                    ->where('organization_id', $this->organizationId);
            })
            ->whereHas('item', function (Builder $query) {
                $query->where('supplier_id', $this->supplierId);
            })
            ->get();

        return $ordersItems->groupBy('item_id')->map(function (Collection $group, string $id) {

            $orderItem = $group->first();

            return [
                'name' => $orderItem->item->name,
                'code' => $orderItem->item->code,
                'article' => $orderItem->item->article,
                'brand' => $orderItem->item->brand,
                'count' => $group->sum('count') * $orderItem->multiplicity,
                'price' => $orderItem->order->price
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
