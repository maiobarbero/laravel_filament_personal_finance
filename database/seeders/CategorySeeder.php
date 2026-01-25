<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
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
                'categories_count' => 5,
            ],
            [
                'name' => 'User Y',
                'email' => 'usery@example.com',
                'categories_count' => 2,
            ],
        ];

        foreach ($usersConfig as $config) {
            $user = User::factory()->create([
                'name' => $config['name'],
                'email' => $config['email'],
            ]);

            $availableNames = (new CategoryFactory())->names;
            
            shuffle($availableNames);

            $selectedNames = array_slice($availableNames, 0, $config['categories_count']);

            Category::factory()
                ->count($config['categories_count'])
                ->sequence(fn ($sequence) => ['name' => $selectedNames[$sequence->index % count($selectedNames)]])
                ->for($user)
                ->create();
        }
    }
}
