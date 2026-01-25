<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Budget;
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
