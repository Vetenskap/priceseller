<?php

namespace App\Services\Bundle;

use App\Exports\BundlesExport;
use App\Imports\BundlesImport;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class BundleService
{
    const PATH = "users/bundles/";
    const FILENAME = "priceseller_bundles";

    public function __construct(public User $user)
    {
    }

    public static function excelImportRules()
    {
        return [
            'МС UUID' => ['nullable'],
            'Код' => ['required'],
            'Наименование' => ['nullable'],
        ];
    }


    public function exportItems(): string
    {
        $uuid = Str::uuid();
        \Excel::store(new BundlesExport($this->user), self::PATH . "$uuid.xlsx", 'public');
        return $uuid;
    }

    public function importItems(string $uuid, string $ext): Collection
    {
        $import = new BundlesImport($this->user);

        \Excel::import($import, self::PATH . $uuid . '.' . $ext, 'public');

        return collect([
            'correct' => $import->correct,
            'error' => $import->error,
            'updated' => $import->updated,
            'deleted' => $import->deleted,
        ]);
    }
}
