<?php

namespace App\Services\Bundle;

use App\Exports\BundleItemsExport;
use App\Imports\BundleItemsImport;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class BundleItemsService
{
    const PATH = "users/bundles/items/";
    const FILENAME = "priceseller_bundles_plural";

    public function __construct(public User $user)
    {
    }

    public static function excelImportRules(): array
    {
        return [
            'Код комплекта' => ['required', 'exists:bundles,code'],
            'Код товара' => ['required', 'exists:items,code'],
            'Кратность отгрузки' => ['required', 'integer', 'min:1'],
        ];
    }


    public function exportItems(): string
    {
        $uuid = Str::uuid();
        \Excel::store(new BundleItemsExport($this->user), self::PATH . "$uuid.xlsx", 'public');
        return $uuid;
    }

    public function importItems(string $uuid, string $ext): Collection
    {
        $import = new BundleItemsImport($this->user);

        \Excel::import($import, self::PATH . $uuid . '.' . $ext, 'public');

        return collect([
            'correct' => $import->correct,
            'error' => $import->error,
            'deleted' => $import->deleted,
        ]);
    }
}
