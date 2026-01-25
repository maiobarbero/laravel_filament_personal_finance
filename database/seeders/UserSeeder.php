<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usersConfig = [
            [
                'name' => 'User X',
                'email' => 'userx@example.com',
            ],
            [
                'name' => 'User Y',
                'email' => 'usery@example.com',
            ],
        ];

        foreach ($usersConfig as $config) {
            User::factory()->create($config);
        }
    }
}
