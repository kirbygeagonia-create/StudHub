<?php

use App\Domain\Identity\Enums\UserRole;
use App\Models\User;

it('casts the role column to the UserRole enum', function () {
    $user = User::factory()->create(['role' => UserRole::Moderator->value]);

    expect($user->role)->toBe(UserRole::Moderator);
    expect($user->role->label())->toBe('Program Moderator');
});

it('defaults new users to the Student role', function () {
    $user = User::factory()->create();

    expect($user->role)->toBe(UserRole::Student);
});

it('exposes a flat list of role values for validation rules', function () {
    expect(UserRole::values())->toEqualCanonicalizing(['student', 'moderator', 'admin']);
});
