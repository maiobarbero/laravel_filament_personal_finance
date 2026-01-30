<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\User;
use Illuminate\Database\Seeder;

class BudgetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // select existing users
        $users = User::all();
        foreach ($users as $user) {
            Budget::factory()->count(3)->create([
                'user_id' => $user->id,
            ]);
        }
    }
}
