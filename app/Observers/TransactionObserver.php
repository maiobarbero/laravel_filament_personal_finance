<?php

namespace App\Observers;

use App\Models\Transaction;

class TransactionObserver
{
    /**
     * Handle the Transaction "created" event.
     */
    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        $bankAccount = $transaction->bankAccount;

        if ($bankAccount) {
            $bankAccount->balance += $transaction->amount;
            $bankAccount->saveQuietly();
        }
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        $originalBankAccountId = $transaction->getOriginal('bank_account_id');
        $originalAmount = $transaction->getOriginal('amount');
        
        // Revert old transaction effect
        if ($originalBankAccountId) {
            $originalBankAccount = \App\Models\BankAccount::find($originalBankAccountId);
            if ($originalBankAccount) {
                $originalBankAccount->balance -= $originalAmount;
                $originalBankAccount->saveQuietly();
            }
        }

        // Apply new transaction effect
        $newBankAccount = \App\Models\BankAccount::find($transaction->bank_account_id);
        if ($newBankAccount) {
            $newBankAccount->balance += $transaction->amount;
            $newBankAccount->saveQuietly();
        }
    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(Transaction $transaction): void
    {
        $bankAccount = $transaction->bankAccount;

        if ($bankAccount) {
            $bankAccount->balance -= $transaction->amount;
            $bankAccount->saveQuietly();
        }
    }
}
