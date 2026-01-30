<?php

use App\Filament\Resources\Categories\Pages\ManageCategories;
use App\Models\Category;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

it('can render page', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(ManageCategories::getUrl())
        ->assertSuccessful();
});

it('can list categories', function () {
    $user = User::factory()->create();
    $category = Category::factory()->for($user)->create();

    Livewire::actingAs($user)
        ->test(ManageCategories::class)
        ->assertCanSeeTableRecords([$category]);
});

it('cannot see other users categories', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $otherCategory = Category::factory()->for($otherUser)->create();

    Livewire::actingAs($user)
        ->test(ManageCategories::class)
        ->assertCanNotSeeTableRecords([$otherCategory]);
});

it('can create category', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(ManageCategories::class)
        ->mountAction('create')
        ->setActionData([
            'name' => 'Food & Drinks',
        ])
        ->callMountedAction()
        ->assertHasNoActionErrors();

    assertDatabaseHas('categories', [
        'user_id' => $user->id,
        'name' => 'Food & Drinks',
    ]);
});

it('can edit category', function () {
    $user = User::factory()->create();
    $category = Category::factory()->for($user)->create([
        'name' => 'Old Category',
    ]);

    Livewire::actingAs($user)
        ->test(ManageCategories::class)
        ->mountTableAction('edit', $category)
        ->setTableActionData([
            'name' => 'Updated Category',
        ])
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors();

    assertDatabaseHas('categories', [
        'id' => $category->id,
        'name' => 'Updated Category',
    ]);
});

it('can delete category', function () {
    $user = User::factory()->create();
    $category = Category::factory()->for($user)->create();

    Livewire::actingAs($user)
        ->test(ManageCategories::class)
        ->mountTableAction('delete', $category)
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors();

    assertDatabaseMissing('categories', [
        'id' => $category->id,
    ]);
});
