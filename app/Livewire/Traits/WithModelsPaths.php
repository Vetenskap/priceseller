<?php

namespace App\Livewire\Traits;

use App\Services\Item\ItemService;
use App\Services\OzonMarketService;
use App\Services\WbMarketService;

trait WithModelsPaths
{
    public array $modelToPath = [
        'App\Models\User' => [
            'path' => ItemService::PATH,
            'filename' => ItemService::FILENAME,
        ],
        'App\Models\OzonMarket' => OzonMarketService::PATH,
        'App\Models\WbMarket' => WbMarketService::PATH,
    ];

    public function getPath(): string
    {
        return is_array($this->modelToPath[get_class($this->model)])
            ? $this->modelToPath[get_class($this->model)]['path']
            : $this->modelToPath[get_class($this->model)];
    }

    public function getFilename()
    {
        return is_array($this->modelToPath[get_class($this->model)])
            ? $this->modelToPath[get_class($this->model)]['filename']
            : $this->model->name;
    }
}
