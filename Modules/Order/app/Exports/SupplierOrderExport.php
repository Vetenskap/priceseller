<?php

namespace Modules\Order\Exports;

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
        $orders = Order::with('orderable.item')
            ->whereHas('orderable')
            ->where('count', '>', 0)
            ->where('state', 'new')
            ->where('organization_id', $this->organizationId)->whereHas('orderable', function (Builder $builder) {
                $builder->whereHas('item', function (Builder $builder) {
                    $builder->where('supplier_id', $this->supplierId);
                });
            })
            ->get();

        return $orders->groupBy('orderable.item.id')->map(function (Collection $group, string $id) {

            $order = $group->first();

            return [
                'name' => $order->orderable->item->name,
                'code' => $order->orderable->item->code,
                'article' => $order->orderable->item->article,
                'brand' => $order->orderable->item->brand,
                'count' => $group->sum('count') * $order->orderable->item->multiplicity,
                'price' => $order->price
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
