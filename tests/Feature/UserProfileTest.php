<?php

use App\Filament\Pages\Auth\EditProfile;
use App\Models\User;
use Filament\Facades\Filament;
use function Pest\Laravel\actingAs;

it('can access profile page', function () {
    // Arrange
    $user = User::factory()->create();

    // Act
    $response = actingAs($user)
        ->get(Filament::getPanel('admin')->getUrl() . '/profile');

    // Assert
    $response->assertSuccessful();
});

it('can update currency and locale', function () {
    // Arrange
    $user = User::factory()->create([
        'currency' => 'EUR',
        'locale' => 'en',
    ]);

    // Act
    actingAs($user);

    \Livewire\Livewire::test(EditProfile::class)
        ->fillForm([
            'name' => $user->name,
            'email' => $user->email,
            'currency' => 'USD',
            'locale' => 'en',
        ])
        ->call('save')
        ->assertHasNoErrors();

    // Assert
    $user->refresh();
    expect($user->currency)->toBe('USD');
    expect($user->locale)->toBe('en');
});

it('formats currency correctly based on user settings', function () {
    // Arrange
    $user = User::factory()->create([
        'currency' => 'GBP',
        'locale' => 'en_GB',
    ]);
    
    // Act & Assert
    actingAs($user);
    $formatted = Illuminate\Support\Number::currency(100, auth()->user()->currency, auth()->user()->locale);
    
    expect($formatted)->toContain('£');
});
