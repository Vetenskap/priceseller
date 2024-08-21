<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Permission;
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

        $modules = [
            ['label' => 'Мой склад', 'name' => 'Moysklad'],
            ['label' => 'Заказы', 'name' => 'Order'],
            ['label' => 'Траст (апи)', 'name' => 'TrastApi'],
            ['label' => 'Восход (апи)', 'name' => 'VoshodApi'],
            ['label' => 'Самсон (апи)', 'name' => 'SamsonApi'],
            ['label' => 'Берг (апи)', 'name' => 'BergApi'],
            ['label' => 'Сималенд (апи)', 'name' => 'SimalandApi'],
        ];

        foreach ($modules as $module) {
            Module::updateOrCreate([
                'name' => $module['name'],
            ], [
                'name' => $module['name'],
                'label' => $module['label']
            ]);
        }

    }
}
