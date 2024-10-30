<?php

namespace App\Console\Commands;

use App\HttpClient\OzonClient\Resources\FBS\PostingUnfulfilled\PostingUnfulfilledList;
use App\Models\OzonMarket;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $collection = collect([
            collect([
                'test' => 3,
                'products' => collect([
                    collect([
                        'test2' => 'E',
                        'item' => collect([
                            'special' => false
                        ])
                    ]),
                    collect([
                        'test2' => 'F',
                        'item' => collect([
                            'special' => false
                        ])
                    ]),
                ])
            ]),
            collect([
                'test' => 2,
                'products' => collect([
                    collect([
                        'test2' => 'C',
                        'item' => collect([
                            'special' => true
                        ])
                    ]),
                    collect([
                        'test2' => 'D',
                        'item' => collect([
                            'special' => false
                        ])
                    ]),
                ])
            ]),
            collect([
                'test' => 1,
                'products' => collect([
                    collect([
                        'test2' => 'A',
                        'item' => collect([
                            'special' => true
                        ])
                    ]),
                    collect([
                        'test2' => 'B',
                        'item' => collect([
                            'special' => true
                        ])
                    ]),
                ])
            ]),
        ]);

        dd(
            $collection->sortBy(fn (Collection $collection) =>
                $collection->get('special') ?
                    $collection->get('special') :
                    $collection->get('products')->sortBy(fn (Collection $collection) =>
                        $collection->get('special') ?
                            $collection->get('special') :
                            $collection->get('item')->sortBy('special'))
            )->toArray()
        );
    }
}
