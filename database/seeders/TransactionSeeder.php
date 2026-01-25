<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = \App\Models\User::with(['bankAccounts', 'categories', 'budgets'])->get();

        foreach ($users as $user) {
            if ($user->bankAccounts->isEmpty() || $user->categories->isEmpty() || $user->budgets->isEmpty()) {
                continue;
            }

            \App\Models\Transaction::factory()
                ->count(10)
                ->state(fn (array $attributes) => [
                    'user_id' => $user->id,
                    'bank_account_id' => $user->bankAccounts->random()->id,
                    'category_id' => $user->categories->random()->id,
                    'budget_id' => $user->budgets->random()->id,
                ])
                ->create();
        }
    }
}
