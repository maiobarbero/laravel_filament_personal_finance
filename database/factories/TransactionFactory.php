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
            'bank_account_id' => fn (array $attributes) => BankAccount::factory()->state(['user_id' => $attributes['user_id']]),
            'description' => fake()->sentence(3),
            'category_id' => fn (array $attributes) => Category::factory()->state(['user_id' => $attributes['user_id']]),
            'budget_id' => fn (array $attributes) => Budget::factory()->state(['user_id' => $attributes['user_id']]),
            'notes' => fake()->sentence(5),
            'amount' => fake()->numberBetween(100, 10000),
        ];
    }
}
