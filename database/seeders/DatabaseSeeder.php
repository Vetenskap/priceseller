<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\NotificationAction;
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
                'value' => 'ozon_five_markets',
                'type' => 'main'
            ],
            [
                'name' => 'Подписка ВБ до 5 кабинетов',
                'value' => 'wb_five_markets',
                'type' => 'main'
            ],
            [
                'name' => 'Подписка ОЗОН до 10 кабинетов',
                'value' => 'ozon_ten_markets',
                'type' => 'main'
            ],
            [
                'name' => 'Подписка ВБ до 10 кабинетов',
                'value' => 'wb_ten_markets',
                'type' => 'main'
            ],
            [
                'name' => 'Админ',
                'value' => 'admin',
                'type' => 'main'
            ],
            [
                'name' => 'Почта',
                'value' => 'emails',
                'type' => 'employee'
            ],
            [
                'name' => 'Поставщики',
                'value' => 'suppliers',
                'type' => 'employee'
            ],
            [
                'name' => 'Товары',
                'value' => 'items',
                'type' => 'employee'
            ],
            [
                'name' => 'Комплекты',
                'value' => 'bundles',
                'type' => 'employee'
            ],
            [
                'name' => 'ОЗОН',
                'value' => 'ozon',
                'type' => 'employee'
            ],
            [
                'name' => 'ВБ',
                'value' => 'wb',
                'type' => 'employee'
            ],
            [
                'name' => 'Организации',
                'value' => 'organizations',
                'type' => 'employee'
            ],
            [
                'name' => 'Склады',
                'value' => 'warehouses',
                'type' => 'employee'
            ],
            [
                'name' => 'Сборочник',
                'value' => 'assembly',
                'type' => 'employee'
            ],
            [
                'name' => 'Редактор контента',
                'value' => 'editorcontent',
                'type' => 'employee'
            ],
            [
                'name' => 'Обработка заказов',
                'value' => 'orderprocess',
                'type' => 'employee'
            ],
            [
                'name' => 'Мой склад',
                'value' => 'moysklad',
                'type' => 'employee'
            ],
            [
                'name' => 'Заказы',
                'value' => 'order',
                'type' => 'employee'
            ],
            [
                'name' => 'Траст (апи)',
                'value' => 'trastapi',
                'type' => 'employee'
            ],
            [
                'name' => 'Восход (апи)',
                'value' => 'voshodapi',
                'type' => 'employee'
            ],
            [
                'name' => 'Самсон (апи)',
                'value' => 'samsonapi',
                'type' => 'employee'
            ],
            [
                'name' => 'Берг (апи)',
                'value' => 'bergapi',
                'type' => 'employee'
            ],
            [
                'name' => 'Сималенд (апи)',
                'value' => 'simalandapi',
                'type' => 'employee'
            ],
            [
                'name' => 'Общие настройки',
                'value' => 'basesettings',
                'type' => 'employee'
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate([
                'name' => $permission['name'],
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
            ['label' => 'Редактор контента', 'name' => 'EditorContent'],
            ['label' => 'Сборочник', 'name' => 'Assembly'],
            ['label' => 'Обработка заказов', 'name' => 'OrderProcess'],
        ];

        foreach ($modules as $module) {
            Module::updateOrCreate([
                'name' => $module['name'],
            ], [
                'name' => $module['name'],
                'label' => $module['label']
            ]);
        }

        $notificationActions = [
            [
                'name' => 'import',
                'label' => 'Импорт',
                'description' => 'Отправлять уведомление об завершении/ошибки любого импорта',
            ],
            [
                'name' => 'export',
                'label' => 'Экспорт',
                'description' => 'Отправлять уведомление об завершении/ошибки любого экспорта',
            ],
            [
                'name' => 'supplier',
                'label' => 'Выгрузка поставщика',
                'description' => 'Отправлять уведомление об окончании/ошибки выгрузки поставщика',
            ]
        ];

        foreach ($notificationActions as $notificationAction) {
            NotificationAction::updateOrCreate([
                'name' => $notificationAction['name'],
            ], $notificationAction);
        }

        // BaseData


    }
}
