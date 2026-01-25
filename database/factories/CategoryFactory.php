<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * The potential names for categories.
     *
     * @var array<string>
     */
    public array $names = [
        'Housing',
        'Transportation',
        'Food provided',
        'Utilities',
        'Clothing',
        'Healthcare',
        'Insurance',
        'Personal Spending',
        'Debt',
        'Savings',
        'Education',
        'Entertainment',
        'Groceries',
        'Restaurants',
        'Travel',
        'Gifts',
        'Donations',
        'Investments',
        'Taxes',
        'Childcare',
        'Pets',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->randomElement($this->names),
        ];
    }
}
