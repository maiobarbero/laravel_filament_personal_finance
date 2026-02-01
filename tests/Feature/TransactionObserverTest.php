<?php

use App\Models\BankAccount;
use App\Models\Transaction;
use App\Models\User;

it('updates bank account balance when transaction is created', function () {
    // Arrange
    $user = User::factory()->create();
    $bankAccount = BankAccount::factory()->for($user)->create(['balance' => 1000]);

    // Act
    Transaction::factory()->for($user)->for($bankAccount)->create(['amount' => 500]);

    // Assert
    expect($bankAccount->refresh()->balance)->toEqual(1500);
});

it('updates bank account balance when transaction is updated', function () {
    // Arrange
    $user = User::factory()->create();
    $bankAccount = BankAccount::factory()->for($user)->create(['balance' => 1000]);
    $transaction = Transaction::factory()->for($user)->for($bankAccount)->create(['amount' => 500]);

    // Act
    $transaction->update(['amount' => 600]);

    // Assert
    expect($bankAccount->refresh()->balance)->toEqual(1600);
});

it('updates bank account balance when transaction is deleted', function () {
    // Arrange
    $user = User::factory()->create();
    $bankAccount = BankAccount::factory()->for($user)->create(['balance' => 1000]);
    $transaction = Transaction::factory()->for($user)->for($bankAccount)->create(['amount' => 500]);

    // Act
    $transaction->delete();

    // Assert
    expect($bankAccount->refresh()->balance)->toEqual(1000);
});

it('updates balances when transaction is moved to another bank account', function () {
    // Arrange
    $user = User::factory()->create();
    $bankAccountA = BankAccount::factory()->for($user)->create(['balance' => 1000]);
    $bankAccountB = BankAccount::factory()->for($user)->create(['balance' => 1000]);
    
    $transaction = Transaction::factory()->for($user)->for($bankAccountA)->create(['amount' => 500]);

    // Act
    $transaction->update(['bank_account_id' => $bankAccountB->id]);

    // Assert
    expect($bankAccountA->refresh()->balance)->toEqual(1000);
    expect($bankAccountB->refresh()->balance)->toEqual(1500);
});
