<?php

namespace Modules\Order\Exports;

use App\Models\OzonItem;
use App\Models\WbItem;
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
        $orders = Order::whereHas('orderable')
            ->with(['orderable' => function ($query) {
                $query->when(
                    $query->getModel() instanceof WbItem, // Проверяем, является ли моделью Wb
                    fn($q) => $q->with('wbitemable') // Загружаем связь 'wbitemable'
                )->when(
                    $query->getModel() instanceof OzonItem, // Проверяем, является ли моделью Ozon
                    fn($q) => $q->with('ozonitemable') // Загружаем связь 'ozonitemable'
                );
            }])
            ->where('count', '>', 0)
            ->where('state', 'new')
            ->where('organization_id', $this->organizationId)->whereHas('orderable', function (Builder $builder) {
                $builder->where(['orderable' => function ($query) {
                    $query->when(
                        $query->getModel() instanceof WbItem, // Проверяем, является ли моделью Wb
                        fn($q) => $q->where('wbitemable.supplier_id') // Загружаем связь 'wbitemable'
                    )->when(
                        $query->getModel() instanceof OzonItem, // Проверяем, является ли моделью Ozon
                        fn($q) => $q->where('ozonitemable.supplier_id') // Загружаем связь 'ozonitemable'
                    );
                }]);
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
