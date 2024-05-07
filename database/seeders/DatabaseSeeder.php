<?php

namespace Database\Seeders;

use App\Models\Email;
use App\Models\EmailSupplier;
use App\Models\Moysklad;
use App\Models\Permission;
use App\Models\Supplier;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\UserPermission;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt(123456),
        ]);

        $permissions = [
            [
                'name' => 'Основная подписка',
                'value' => 'main_sub',
            ],
            [
                'name' => 'Подписка на Мой Склад',
                'value' => 'ms_sub',
            ],
            [
                'name' => 'Подписка на Авито',
                'value' => 'avito_sub',
            ],
            [
                'name' => 'Админ',
                'value' => 'admin'
            ]
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        $userPermissions = Permission::all()->map(function (Permission $permission) {
            return [
                'permission_id' => $permission->id,
                'user_id' => 1,
                'expires' => now()->addYear()->timestamp,
            ];
        })->all();

        foreach ($userPermissions as $userPermission) {
            UserPermission::create($userPermission);
        }

        Moysklad::create([
            'user_id' => 1,
            'api_key' => '90d841b0-14ce-11ee-0a80-0631000b827a',
            'name' => 'Основной',
        ]);

        $suppliers = [
            [
                'name' => 'Траст',
                'user_id' => 1,
                'id' => '9bd1f334-9270-429e-b225-8382d3f16ba9'
            ],
            [
                'name' => 'Восход',
                'user_id' => 1,
                'id' => '9bd1f334-9270-429e-b335-8382d3f16ba9'
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }

        Email::create([
            'name' => 'avprice',
            'address' => 'vetenskap@bk.ru',
            'password' => 'WmGjJBFan0EGta6BtDUw',
            'id' => '9bd1f334-9270-429e-b225-8382d7f16ba4',
            'user_id' => 1
        ]);

        $emailSuppliers = [
            [
                'supplier_id' =>  '9bd1f334-9270-429e-b225-8382d3f16ba9',
                'email_id' => '9bd1f334-9270-429e-b225-8382d7f16ba4',
                'email' => 'vetenskap2@yandex.ru',
                'filename' => 'АвтоТраст',
                'header_start' => 1,
                'header_article_supplier' => 'Артикул',
                'header_article_manufacturer' => '',
                'header_brand' => 'Производитель',
                'header_price' => 'Цена',
                'header_count' => 'Остаток'
            ],
            [
                'supplier_id' =>  '9bd1f334-9270-429e-b335-8382d3f16ba9',
                'email_id' => '9bd1f334-9270-429e-b225-8382d7f16ba4',
                'email' => 'vetenskap2@yandex.ru',
                'filename' => 'price',
                'header_start' => 1,
                'header_article_supplier' => 'Артикул',
                'header_article_manufacturer' => 'Артикул производителя',
                'header_brand' => 'Номенклатура.Производитель',
                'header_price' => 'Цена',
                'header_count' => 'Остаток'
            ],
        ];

        foreach ($emailSuppliers as $emailSupplier) {
            EmailSupplier::create($emailSupplier);
        }

    }
}
