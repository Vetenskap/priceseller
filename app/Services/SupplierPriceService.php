<?php

namespace App\Services;

use App\Exceptions\Components\SupplierPriceHandler\SupplierPriceHandlerException;
use App\Imports\SupplierImport;
use App\Models\EmailSupplier;
use App\Models\Supplier;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Storage;

class SupplierPriceService
{

    protected string $disk;
    protected string $path;
    protected EmailSupplier $emailSupplier;

    /**
     * @param string $disk
     * @param string $path
     * @param Supplier $supplier
     */
    public function __construct(string $path, string $emailSupplierId, string $disk = 'public')
    {
        Context::push('constructor', [
            'path' => $path,
            'emailSupplierId' => $emailSupplierId,
            'disk' => $disk
        ]);

        if (!in_array($disk, array_keys(config('filesystems.disks')))) throw new SupplierPriceHandlerException('do not found the disk');
        if (!Storage::disk($disk)->exists($path)) throw new SupplierPriceHandlerException('do not found the file');

        $this->disk = $disk;
        $this->path = $path;
        $this->emailSupplier = EmailSupplier::findOrFail($emailSupplierId);
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

    protected function xlsxHandle(): void
    {

        $reader = ReaderEntityFactory::createXLSXReader();

        try {
            $reader->open(Storage::disk($this->disk)->path($this->path));
        } catch (IOException $e) {
            $this->otherHandle();
        }

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {

                $data = $row->toArray();

                Context::push('xlsxHandle', [
                    'current_row' => $data
                ]);

                dump($data);
                continue;

            }
        }
    }


    protected function otherHandle()
    {
        (new SupplierImport($this->emailSupplier->id))->queue($this->path, $this->disk);
    }
}
