<?php

namespace Modules\Order\Exports;

use Illuminate\Database\Eloquent\Builder;
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
            ->where('count', '>', 0)
            ->where('state', 'new')
            ->where('organization_id', $this->organizationId)->whereHas('orderable', function (Builder $builder) {
                $builder->whereHas('item', function (Builder $builder) {
                    $builder->where('supplier_id', $this->supplierId);
                });
            })
            ->get();

        return $orders->map(function (Order $order) {
            if ($order->orderable->item->code == '4-063497') {
                dd($order);
            }
            return [
                'name' => $order->orderable->item->name,
                'code' => $order->orderable->item->code,
                'article' => $order->orderable->item->article,
                'brand' => $order->orderable->item->brand,
                'count' => $order->count * $order->orderable->item->multiplicity,
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
