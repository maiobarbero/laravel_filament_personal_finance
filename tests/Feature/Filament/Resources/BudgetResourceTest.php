<?php

use App\Enums\BudgetType;
use App\Filament\Resources\Budgets\Pages\ManageBudgets;
use App\Models\Budget;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

it('can render page', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(ManageBudgets::getUrl())
        ->assertSuccessful();
});

it('can list budgets', function () {
    $user = User::factory()->create();
    $budget = Budget::factory()->for($user)->create();

    Livewire::actingAs($user)
        ->test(ManageBudgets::class)
        ->assertCanSeeTableRecords([$budget]);
});

it('cannot see other users budgets', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $otherBudget = Budget::factory()->for($otherUser)->create();

    Livewire::actingAs($user)
        ->test(ManageBudgets::class)
        ->assertCanNotSeeTableRecords([$otherBudget]);
});

it('can create budget', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(ManageBudgets::class)
        ->mountAction('create')
        ->setActionData([
            'name' => 'Monthly Groceries',
            'amount' => '500.00',
            'type' => BudgetType::Reset->value,
        ])
        ->callMountedAction()
        ->assertHasNoActionErrors();

    assertDatabaseHas('budgets', [
        'user_id' => $user->id,
        'name' => 'Monthly Groceries',
        'amount' => 50000,
        'type' => BudgetType::Reset->value,
    ]);
});

it('can edit budget', function () {
    $user = User::factory()->create();
    $budget = Budget::factory()->for($user)->create([
        'name' => 'Old Budget',
        'amount' => 10000,
    ]);

    Livewire::actingAs($user)
        ->test(ManageBudgets::class)
        ->mountTableAction('edit', $budget)
        ->setTableActionData([
            'name' => 'New Budget',
            'amount' => '250.50',
            'type' => BudgetType::Rollover->value,
        ])
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors();

    assertDatabaseHas('budgets', [
        'id' => $budget->id,
        'name' => 'New Budget',
        'amount' => 25050,
        'type' => BudgetType::Rollover->value,
    ]);
});

it('can delete budget', function () {
    $user = User::factory()->create();
    $budget = Budget::factory()->for($user)->create();

    Livewire::actingAs($user)
        ->test(ManageBudgets::class)
        ->mountTableAction('delete', $budget)
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors();

    assertDatabaseMissing('budgets', [
        'id' => $budget->id,
    ]);
});
