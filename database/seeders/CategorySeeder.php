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
            'userx@example.com' => 5,
            'usery@example.com' => 2,
        ];

        foreach ($usersConfig as $email => $count) {
            $user = User::where('email', $email)->first();
            
            if (! $user) {
                continue;
            }

            $availableNames = (new CategoryFactory())->names;
            
            shuffle($availableNames);

            $selectedNames = array_slice($availableNames, 0, $count);

            Category::factory()
                ->count($count)
                ->sequence(fn ($sequence) => ['name' => $selectedNames[$sequence->index % count($selectedNames)]])
                ->for($user)
                ->create();
        }
    }
}
