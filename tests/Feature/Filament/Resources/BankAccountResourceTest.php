<?php

use App\Filament\Resources\BankAccounts\Pages\ManageBankAccounts;
use App\Models\BankAccount;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

it('can render page', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(ManageBankAccounts::getUrl())
        ->assertSuccessful();
});

it('can list bank accounts', function () {
    $user = User::factory()->create();
    $bankAccount = BankAccount::factory()->for($user)->create();

    Livewire::actingAs($user)
        ->test(ManageBankAccounts::class)
        ->assertCanSeeTableRecords([$bankAccount]);
});

it('cannot see other users bank accounts', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $otherBankAccount = BankAccount::factory()->for($otherUser)->create();

    Livewire::actingAs($user)
        ->test(ManageBankAccounts::class)
        ->assertCanNotSeeTableRecords([$otherBankAccount]);
});

it('can create bank account', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(ManageBankAccounts::class)
        ->mountAction('create')
        ->setActionData([
            'name' => 'My Main Bank',
            'balance' => '1000.50',
        ])
        ->callMountedAction()
        ->assertHasNoActionErrors();

    assertDatabaseHas('bank_accounts', [
        'user_id' => $user->id,
        'name' => 'My Main Bank',
        'balance' => 100050, // 1000.50 * 100
    ]);
});

it('can edit bank account', function () {
    $user = User::factory()->create();
    $bankAccount = BankAccount::factory()->for($user)->create([
        'name' => 'Old Name',
        'balance' => 5000,
    ]);

    Livewire::actingAs($user)
        ->test(ManageBankAccounts::class)
        ->mountTableAction('edit', $bankAccount)
        ->setTableActionData([
            'name' => 'New Name',
            'balance' => '200.00',
        ])
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors();

    assertDatabaseHas('bank_accounts', [
        'id' => $bankAccount->id,
        'name' => 'New Name',
        'balance' => 20000,
    ]);
});

it('can delete bank account', function () {
    $user = User::factory()->create();
    $bankAccount = BankAccount::factory()->for($user)->create();

    Livewire::actingAs($user)
        ->test(ManageBankAccounts::class)
        ->mountTableAction('delete', $bankAccount)
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors();

    assertDatabaseMissing('bank_accounts', [
        'id' => $bankAccount->id,
    ]);
});
