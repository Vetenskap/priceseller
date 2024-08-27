<?php

return [
    'name' => 'Мой склад',
    'main_route' => 'moysklad.index',

    'available_webhooks' => [
        [
            'action' => 'UPDATE',
            'type' => 'warehouses',
            'name' => 'Склады (остатки)'
        ],
        [
            'action' => 'UPDATE',
            'type' => 'product',
            'name' => 'Товар (обновление)'
        ],
        [
            'action' => 'CREATE',
            'type' => 'customerorder',
            'name' => 'Заказ (создание)'
        ],
        [
            'action' => 'CREATE',
            'type' => 'product',
            'name' => 'Товар (создание)'
        ],
        [
            'action' => 'DELETE',
            'type' => 'product',
            'name' => 'Товар (удаление)'
        ],
        [
            'action' => 'CREATE',
            'type' => 'demand',
            'name' => 'Отгрузка (создание)'
        ],
    ]
];
