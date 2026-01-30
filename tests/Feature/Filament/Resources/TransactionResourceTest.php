<?php

use App\Filament\Resources\Transactions\Pages\CreateTransaction;
use App\Filament\Resources\Transactions\Pages\EditTransaction;
use App\Filament\Resources\Transactions\Pages\ListTransactions;
use App\Models\BankAccount;
use App\Models\Transaction;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

it('can render list page', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(ListTransactions::getUrl())
        ->assertSuccessful();
});

it('can list transactions', function () {
    $user = User::factory()->create();
    $transaction = Transaction::factory()->for($user)->create();

    Livewire::actingAs($user)
        ->test(ListTransactions::class)
        ->assertCanSeeTableRecords([$transaction]);
});

it('cannot see other users transactions', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $otherTransaction = Transaction::factory()->for($otherUser)->create();

    Livewire::actingAs($user)
        ->test(ListTransactions::class)
        ->assertCanNotSeeTableRecords([$otherTransaction]);
});

it('can render create page', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(CreateTransaction::getUrl())
        ->assertSuccessful();
});

it('can create transaction', function () {
    $user = User::factory()->create();
    $bankAccount = BankAccount::factory()->for($user)->create();

    Livewire::actingAs($user)
        ->test(CreateTransaction::class)
        ->fillForm([
            'date' => '2026-01-30',
            'transaction_type' => 'expense',
            'amount' => '150.00',
            'description' => 'Test Transaction',
            'bank_account_id' => $bankAccount->id,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas('transactions', [
        'user_id' => $user->id,
        'bank_account_id' => $bankAccount->id,
        'description' => 'Test Transaction',
        'amount' => -15000, // 150.00 * 100, negative for expense
    ]);
});

it('cannot create transaction for other user bank account', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $otherBankAccount = BankAccount::factory()->for($otherUser)->create();

    Livewire::actingAs($user)
        ->test(CreateTransaction::class)
        ->fillForm([
            'date' => '2026-01-30',
            'transaction_type' => 'expense',
            'amount' => '150.00',
            'description' => 'Test Transaction',
            'bank_account_id' => $otherBankAccount->id,
        ])
        ->call('create')
        ->assertHasFormErrors(['bank_account_id']);
});

it('can render edit page', function () {
    $user = User::factory()->create();
    $transaction = Transaction::factory()->for($user)->create();

    actingAs($user)
        ->get(EditTransaction::getUrl(['record' => $transaction]))
        ->assertSuccessful();
});

it('cannot render edit page for other user transaction', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $otherTransaction = Transaction::factory()->for($otherUser)->create();

    actingAs($user)
        ->get(EditTransaction::getUrl(['record' => $otherTransaction]))
        ->assertNotFound();
});

it('can edit transaction', function () {
    $user = User::factory()->create();
    $bankAccount = BankAccount::factory()->for($user)->create();
    $transaction = Transaction::factory()->for($user)->create([
        'bank_account_id' => $bankAccount->id,
        'description' => 'Old Description',
        'amount' => -5000,
    ]);

    Livewire::actingAs($user)
        ->test(EditTransaction::class, ['record' => $transaction->id])
        ->fillForm([
            'description' => 'Updated Description',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas('transactions', [
        'id' => $transaction->id,
        'description' => 'Updated Description',
    ]);
});

it('can delete transaction', function () {
    $user = User::factory()->create();
    $transaction = Transaction::factory()->for($user)->create();

    Livewire::actingAs($user)
        ->test(EditTransaction::class, ['record' => $transaction->id])
        ->callAction(DeleteAction::class)
        ->assertRedirect();

    assertDatabaseMissing('transactions', [
        'id' => $transaction->id,
    ]);
});
