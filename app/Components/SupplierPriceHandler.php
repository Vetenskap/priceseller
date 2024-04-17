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
    protected string $disk;
    protected string $path;
    protected Supplier $supplier;

    /**
     * @param string $disk
     * @param string $path
     * @param Supplier $supplier
     */
    public function __construct(string $path, string $supplierId, string $disk = 'public')
    {
        Context::push('constructor', [
            'path' => $path,
            'supplierId' => $supplierId,
            'disk' => $disk
        ]);

        if (!in_array($disk, array_keys(config('filesystems.disks')))) throw new SupplierPriceHandlerException('do not found the disk');
        if (!Storage::disk($disk)->exists($path)) throw new SupplierPriceHandlerException('do not found the file');

        $this->disk = $disk;
        $this->path = $path;
        $this->supplier = Supplier::findOrFail($supplierId);
    }

    public function handle(): void
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

    public function xlsxHandle(): void
    {
        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->open(Storage::disk($this->disk)->path($this->path));

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {

                Context::push('xlsxHandle', [
                    'current_row' => $row->toArray()
                ]);

                dump($row->toArray());
            }
        }
    }

    protected function otherHandle()
    {
        (new SupplierImport($this->supplier->id))->queue($this->path, $this->disk);
    }

}
