<?php

namespace Database\Seeders;

use App\Models\Email;
use App\Models\EmailSupplier;
use App\Models\EmailSupplierStockValue;
use App\Models\Moysklad;
use App\Models\OzonMarket;
use App\Models\Permission;
use App\Models\Supplier;
use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\UserPermission;
use App\Models\WbMarket;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $permissions = [
            [
                'name' => 'Подписка ОЗОН до 5 кабинетов',
                'value' => 'ozon_five_markets'
            ],
            [
                'name' => 'Подписка ВБ до 5 кабинетов',
                'value' => 'wb_five_markets'
            ],
            [
                'name' => 'Подписка ОЗОН до 10 кабинетов',
                'value' => 'ozon_ten_markets'
            ],
            [
                'name' => 'Подписка ВБ до 10 кабинетов',
                'value' => 'wb_ten_markets'
            ],
            [
                'name' => 'Админ',
                'value' => 'admin'
            ]
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate([
                'name' => $permission['name'],
                'value' => $permission['value'],
            ], $permission);
        }

//        $userPermissions = Permission::all()->map(function (Permission $permission) {
//            return [
//                'permission_id' => $permission->id,
//                'user_id' => 1,
//                'expires' => now()->addYear()->timestamp,
//            ];
//        })->all();
//
//        foreach ($userPermissions as $userPermission) {
//            UserPermission::updateOrCreate([
//                'permission_id' => $userPermission['permission_id'],
//                'user_id' => $userPermission['user_id'],
//            ], $userPermission);
//        }
//
//        Moysklad::updateOrCreate([
//            'user_id' => 1,
//            'api_key' => '90d841b0-14ce-11ee-0a80-0631000b827a',
//            'name' => 'Основной',
//        ], [
//            'user_id' => 1,
//            'api_key' => '90d841b0-14ce-11ee-0a80-0631000b827a',
//            'name' => 'Основной',
//        ]);
//
//        $suppliers = [
//            [
//                'name' => 'Шатэ',
//                'user_id' => 1,
//                'id' => '9bd1f334-9270-429e-b225-8382d3f16ba9'
//            ],
//            [
//                'name' => 'Берг',
//                'user_id' => 1,
//                'id' => '9bd1f334-9270-429e-b335-8382d3f16ba9'
//            ],
//            [
//                'name' => 'Росско',
//                'user_id' => 1,
//                'id' => '9bd1f334-9270-429e-b335-8382d3f27ba9'
//            ],
//        ];
//
//        foreach ($suppliers as $supplier) {
//            Supplier::updateOrCreate([
//                'id' => $supplier['id']
//            ], $supplier);
//        }
//
//        Email::updateOrCreate([
//            'address' => 'avprice@mail.ru',
//        ], [
//            'name' => 'avprice',
//            'address' => 'avprice@mail.ru',
//            'password' => 'pprj94tL1v0jjpTfqA06',
//            'id' => '9bd1f334-9270-429e-b225-8382d7f16ba4',
//            'user_id' => 1
//        ]);
//
//        $emailSuppliers = [
//            [
//                'supplier_id' => '9bd1f334-9270-429e-b225-8382d3f16ba9',
//                'email_id' => '9bd1f334-9270-429e-b225-8382d7f16ba4',
//                'email' => 'prices_export@shate-m.com',
//                'filename' => 'export',
//                'header_article' => 2,
//                'header_brand' => 1,
//                'header_price' => 7,
//                'header_count' => 4
//            ],
//            [
//                'supplier_id' => '9bd1f334-9270-429e-b335-8382d3f16ba9',
//                'email_id' => '9bd1f334-9270-429e-b225-8382d7f16ba4',
//                'email' => 'noreply@berg.ru',
//                'filename' => 'BERG',
//                'header_article' => 1,
//                'header_brand' => 3,
//                'header_price' => 6,
//                'header_count' => 5
//            ],
//            [
//                'supplier_id' => '9bd1f334-9270-429e-b335-8382d3f27ba9',
//                'email_id' => '9bd1f334-9270-429e-b225-8382d7f16ba4',
//                'email' => 'price@rossko.ru',
//                'filename' => 'rossko',
//                'header_article' => 1,
//                'header_brand' => 2,
//                'header_price' => 7,
//                'header_count' => 9
//            ],
//        ];
//
//        foreach ($emailSuppliers as $emailSupplier) {
//            EmailSupplier::updateOrCreate([
//                'supplier_id' => $emailSupplier['supplier_id'],
//                'email_id' => $emailSupplier['email_id'],
//                'email' => $emailSupplier['email'],
//            ], $emailSupplier);
//        }
//
//        $ozonMarkets = collect([
//            [
//                'id' => '7bd1f334-9270-459d-b335-8382d3f27ba9',
//                'name' => 'autoon',
//                'client_id' => 282498,
//                'api_key' => '9360c0d4-ee1e-4164-a036-83a8a825332e',
//                'min_price_percent' => 1,
//                'max_price_percent' => 37,
//                'seller_price_percent' => 8,
//                'acquiring' => 3,
//                'last_mile' => 5.5,
//                'max_mile' => 500,
//                'user_id' => 1
//            ],
//            [
//                'id' => '7bd1f334-9270-459d-b335-7482d3f28ba9',
//                'name' => 'axauto',
//                'client_id' => 180191,
//                'api_key' => '469363b1-797a-4688-9783-134d369d2bdf',
//                'min_price_percent' => 1,
//                'max_price_percent' => 37,
//                'seller_price_percent' => 8,
//                'acquiring' => 3,
//                'last_mile' => 5.5,
//                'max_mile' => 500,
//                'user_id' => 1
//            ],
//        ]);
//
//        $ozonMarkets->each(function (array $ozonMarket) {
//            OzonMarket::updateOrCreate([
//                'id' => $ozonMarket['id']
//            ], $ozonMarket);
//        });
//
//        $wbMarkets = collect([
//            [
//                'id' => '3be2f334-9270-459d-b335-7482d3f28ba3',
//                'name' => 'iviko',
//                'api_key' => 'eyJhbGciOiJFUzI1NiIsImtpZCI6IjIwMjMxMDI1djEiLCJ0eXAiOiJKV1QifQ.eyJlbnQiOjEsImV4cCI6MTcxODMxMDQzMCwiaWQiOiI1NjYzYzc4NS0xNjAxLTQ0NjQtYjk4My1mZDU5YTE4YjhmYTYiLCJpaWQiOjQ5MjM4NDQ1LCJvaWQiOjEyODgzNTYsInMiOjU4LCJzYW5kYm94IjpmYWxzZSwic2lkIjoiZjNhZmRiMmMtNjRhMC00ZmYwLTlmODItYzI0YTBlMGIxZTA1IiwidWlkIjo0OTIzODQ0NX0.uD1jCg9I1xLM1NY_FN8cbtdyR_kI1YtRYf1t2tKPR5uDq_f81LiNHLaDitO9ibT5seUTQ01Ga77ib_WjGHOsIQ',
//                'basic_logistics' => 35,
//                'price_one_liter' => 9,
//                'volume' => 1,
//                'user_id' => 1
//            ]
//        ]);
//
//        $wbMarkets->each(function (array $wbMarket) {
//            WbMarket::updateOrCreate([
//                'id' => $wbMarket['id']
//            ], $wbMarket);
//        });
//
//        EmailSupplierStockValue::updateOrCreate([
//            'email_supplier_id' => 3,
//        ], [
//            'email_supplier_id' => 3,
//            'name' => '10-100',
//            'value' => 100
//        ]);

    }
}
