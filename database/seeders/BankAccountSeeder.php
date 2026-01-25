<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BankAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = \App\Models\User::all();
        foreach ($users as $user) {
            \App\Models\BankAccount::factory()->count(1)->create([
                'user_id' => $user->id,
            ]);
        }
    }
}
