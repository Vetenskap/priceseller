<?php

namespace App\Services\Item;

use App\Exports\ItemsExport;
use App\Imports\ItemsImport;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ItemService
{
    const PATH = "users/main/";
    const FILENAME = "priceseller";

    public function __construct(public User $user)
    {
    }

    public function exportItems(): string
    {
        $uuid = Str::uuid();
        \Excel::store(new ItemsExport($this->user->id), self::PATH . "$uuid.xlsx", 'public');
        return $uuid;
    }

    public function importItems(string $uuid, string $ext): Collection
    {
        $import = new ItemsImport($this->user->id);

        Log::debug('Импорт прямо сейчас..');

        \Excel::import($import, self::PATH . $uuid . '.' . $ext, 'public');

        Log::debug('Импорт завершён, возвращаем результат');

        return collect([
            'correct' => $import->correct,
            'error' => $import->error,
            'updated' => $import->updated,
            'deleted' => $import->deleted,
        ]);
    }
}
