<?php

namespace Database\Seeders;

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

        Supplier::create([
            'name' => 'Траст',
            'user_id' => 1,
            'id' => '9bd1f334-9270-429e-b225-8382d3f16ba9'
        ]);
    }
}
