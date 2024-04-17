<?php

namespace App\Components;

use App\Exceptions\Components\SupplierPriceHandler\SupplierPriceHandlerException;
use App\Imports\SupplierImport;
use App\Models\Supplier;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Storage;

class SupplierPriceHandler
{
    private Supplier $supplier;
    private string $path;
    private string $disk;

    /**
     * @param Supplier $supplier
     */
    public function __construct(int $supplierId, string $path, string $disk = 'public')
    {
        Context::push('SupplierPriceHandler', [
            'path' => $path,
            'disk' => $disk,
            'supplierId' => $supplierId
        ]);

        if (!in_array($disk, array_keys(config('filesystems.disks')))) {
            throw new SupplierPriceHandlerException('do not found disk');
        }

        if (!Storage::disk($disk)->exists($path)) {
            throw new SupplierPriceHandlerException('do not found file');
        }

        $this->supplier = Supplier::findOrFail($supplierId);
        $this->disk = $disk;
        $this->path = $path;
    }


    public function handle()
    {
        $ext = pathinfo($this->path, PATHINFO_EXTENSION);

        switch ($ext) {
            case 'xlsx':
                $this->xlsxHandle();
                break;
            default:
                $this->otherHandle();
                break;
        }
    }

    private function xlsxHandle()
    {
        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->open(Storage::disk($this->disk)->path($this->path));

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {

                Context::push('SupplierPriceHandler', [
                    'current_row' => $row->toArray()
                ]);

                dd($row->toArray());
            }
        }
    }

    private function otherHandle()
    {
        (new SupplierImport($this->supplier->id))->queue($this->path, $this->disk);
    }
}
