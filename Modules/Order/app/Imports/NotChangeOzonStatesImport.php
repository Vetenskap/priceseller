<?php

namespace Modules\Order\Imports;

use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\Order\Models\NotChangeOzonState;

class NotChangeOzonStatesImport implements ToCollection, WithHeadingRow
{
    public function __construct(public User $user)
    {
    }


    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        $collection->each(function (Collection $row) {

            if ($item = $this->user->items()->where('code', $row->get('Код клиента'))->first()) {
                if ($row->get('Удалить') === 'Да') {
                    NotChangeOzonState::where('user_id', $this->user->id)->where('item_id', $item->id)->delete();
                    return;
                }

                NotChangeOzonState::updateOrCreate([
                    'user_id' => $this->user->id,
                    'item_id' => $item->id
                ], [
                    'user_id' => $this->user->id,
                    'item_id' => $item->id
                ]);
            }
        });
    }
}
