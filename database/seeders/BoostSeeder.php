<?php

namespace Database\Seeders;

use App\Enums\BudgetType;
use App\Models\BankAccount;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BoostSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::find(1);

        if (!$user) {
            $this->command->info('User with ID 1 not found. Creating one...');
            $user = User::factory()->create([
                'id' => 1,
                'name' => 'admin',
                'email' => 'admin@admin.com',
            ]);
        }

        $this->command->info('Seeding data for User ID: ' . $user->id);

        // Clear existing data for this user to avoid duplication if run multiple times
        // OR just append. The prompt says "Add 1000 transaction", implies appending or fresh.
        // But to be consistent, I will assume we are adding to potential existing, but since I am creating specific categories/budgets, check uniqueness.

        // 1. Create Bank Accounts
        $checking = BankAccount::firstOrCreate(
            ['user_id' => $user->id, 'name' => 'Main Checking'],
            ['balance' => 1000] // Initial balance, will be affected by transactions? Usually balance is calculated or just a field. Model has 'balance'. I'll set a start.
        );

        $savings = BankAccount::firstOrCreate(
            ['user_id' => $user->id, 'name' => 'High Yield Savings'],
            ['balance' => 10000]
        );

        $bankAccounts = [$checking, $savings];

        // 2. Create Categories
        $categoriesNames = ['Salary', 'Rent', 'Groceries', 'Utilities', 'Entertainment'];
        $categories = [];
        foreach ($categoriesNames as $name) {
            $categories[$name] = Category::firstOrCreate(
                ['user_id' => $user->id, 'name' => $name]
            );
        }

        // 3. Create Budgets
        $budgetConfigs = [
            ['name' => 'Rent Budget', 'amount' => 1500, 'type' => BudgetType::Rollover],
            ['name' => 'Groceries Budget', 'amount' => 600, 'type' => BudgetType::Reset],
            ['name' => 'Utilities Budget', 'amount' => 200, 'type' => BudgetType::Reset],
            ['name' => 'Entertainment Budget', 'amount' => 300, 'type' => BudgetType::Reset],
            ['name' => 'Savings Goal', 'amount' => 500, 'type' => BudgetType::Rollover],
            ['name' => 'Holiday Fund', 'amount' => 1000, 'type' => BudgetType::Rollover],
        ];

        $budgets = [];
        foreach ($budgetConfigs as $config) {
            $budgets[$config['name']] = Budget::firstOrCreate(
                ['user_id' => $user->id, 'name' => $config['name']],
                ['amount' => $config['amount'], 'type' => $config['type']]
            );
        }

        // 4. Create 1000 Transactions
        $transactions = [];
        $startDate = Carbon::create(2025, 1, 1);
        $endDate = Carbon::create(2026, 1, 31);

        // Let's generate them.
        for ($i = 0; $i < 1000; $i++) {
            $date = $startDate->copy()->addSeconds(rand(0, $endDate->diffInSeconds($startDate)));

            // Randomly decide type based on category weights
            // Salary: Low probability (once a month approx)
            // Rent: Low probability (once a month)
            // Others: Higher probability

            $rand = rand(1, 100);

            $category = null;
            $amount = 0;
            $description = '';
            $bankAccount = $checking; // Default to checking
            $budget = null;

            // Simplified logic to distribute meaningful data
            if ($rand <= 5) {
                // Salary (~5% chance? maybe too high for 1000 transactions over 1 year = ~80 transactions. Should be 12-13. 
                // Let's just randomize broadly and correct logic slightly or just accept randomness.)
                // actually 1000 transactions in 13 months is ~2.5 per day.
                // Salary should be ~13 total. 
                // Let's force Salary on specific days? No, random is fine but weight should be low.
                // Re-think: Random category selection.

                $catName = array_rand($categories);
                // Bias against Salary/Rent to avoid having 200 salaries.
            }

            // Weighted selection
            $typeRoll = rand(1, 100);

            if ($typeRoll <= 2) { // 2% chance for Salary ~ 20 transactions
                $catName = 'Salary';
            } elseif ($typeRoll <= 4) { // 2% chance for Rent ~ 20 transactions
                $catName = 'Rent';
            } elseif ($typeRoll <= 40) { // 36% Groceries
                $catName = 'Groceries';
            } elseif ($typeRoll <= 55) { // 15% Utilities
                $catName = 'Utilities';
            } else { // 45% Entertainment
                $catName = 'Entertainment';
            }

            $currentCategory = $categories[$catName];

            if ($catName === 'Salary') {
                $amount = rand(3000, 5000); // Income
                $description = 'Monthly Salary';
                // No budget for income usually, or maybe Savings Goal?
            } elseif ($catName === 'Rent') {
                $amount = -rand(1400, 1600);
                $description = 'Monthly Rent';
                $budget = $budgets['Rent Budget'];
            } elseif ($catName === 'Groceries') {
                $amount = -rand(20, 200);
                $description = 'Grocery Store ' . rand(1, 5);
                $budget = $budgets['Groceries Budget'];
            } elseif ($catName === 'Utilities') {
                $amount = -rand(50, 250);
                $description = 'Utility Bill';
                $budget = $budgets['Utilities Budget'];
            } else { // Entertainment
                $amount = -rand(10, 150);
                $description = 'Fun / Movies / Games';
                $budget = $budgets['Entertainment Budget'];
            }

            // Occasional savings transfer
            if ($catName === 'Salary' && rand(1, 2) === 1) {
                // Not a transaction in strict sense if we are just logging lines, 
                // but let's just stick to the generated one.
            }

            // Randomly assign to savings account if entertainment or utility? 
            // Nah, mostly checking.
            if ($catName === 'Entertainment' && rand(1, 10) === 1) {
                $bankAccount = $savings;
            }

            $transactions[] = [
                'user_id' => $user->id,
                'bank_account_id' => $bankAccount->id,
                'category_id' => $currentCategory->id,
                'budget_id' => $budget?->id,
                'description' => $description,
                'amount' => $amount * 100, // Store in cents because no cast here on insert array, wait!
                // We are using `Transaction::insert` or `create`?
                // If I use `insert`, casts are NOT applied. I must store raw integer.
                // If I use `create`, casts ARE applied.
                // `amount` is MoneyCast.
                // MoneyCast set: inputs float/int value, multiplies by 100 and casts to int.
                // If I pass 1500 (float) -> stores 150000.
                // My logic above `$amount` is in dollars (e.g. 1500).
                // If I use create, I pass 1500.
                // If I use insert, I pass 150000.
                // Loop 1000 times `create` might be slow. `insert` is faster.
                // Let's use `insert` chunks.
                'note' => 'Generated transaction',
                'date' => $date->format('Y-m-d H:i:s'),
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ];
        }

        foreach (array_chunk($transactions, 100) as $chunk) {
            Transaction::insert($chunk); // Raw insert, so amount must be in cents.
        }

        $this->command->info('Successfully created ' . count($transactions) . ' transactions.');

        // Update balances? BankAccount usually requires manual update or observers.
        // Assuming no observers for now or user doesn't care about synced balance for this seed.
        // But let's calculate and update balances just in case.

        $checkingBalance = Transaction::where('bank_account_id', $checking->id)->sum('amount');
        $savingsBalance = Transaction::where('bank_account_id', $savings->id)->sum('amount');

        // Update directly
        // Note: 'balance' is also casted in BankAccount model.
        // If updating via model -> update(['balance' => $val]) -> uses set cast (expects dollars).
        // If updating via DB::table -> expects cents.

        // $checkingBalance is in CENTS because Sum on database column returns raw value.
        // We want to save CENTS to DB if using DB facade, or Dollars if using Model.

        // Let's use DB to be safe and raw.
        // wait, $checkingBalance is sum of 'amount' column. 'amount' column is cents. 
        // So $checkingBalance is total cents.

        // BankAccount 'balance' column presumably stores cents too.
        // Let's check BankAccount model again.
        // casts balance => MoneyCast.
        // MoneyCast set -> *100.
        // So model expects dollars.

        // I will use raw update to keep it simple and consistent.
        // DB::table('bank_accounts')->where('id', $checking->id)->update(['balance' => $checkingBalance]);

        // Adding initial balance
        $checking->update(['balance' => ($checkingBalance / 100) + 1000]);
        $savings->update(['balance' => ($savingsBalance / 100) + 10000]);

        $this->command->info('Balances updated.');
    }
}
