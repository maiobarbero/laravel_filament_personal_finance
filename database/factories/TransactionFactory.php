<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\BankAccount;
use App\Models\Category;
use App\Models\Budget;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'bank_account_id' => BankAccount::factory(),
            'description' => fake()->sentence(3),
            'category_id' => Category::factory(),
            'budget_id' => Budget::factory(),
            'notes' => fake()->sentence(5),
            'amount' => fake()->numberBetween(100, 10000),
        ];
    }
}
