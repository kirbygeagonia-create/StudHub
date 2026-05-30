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
    expect(UserRole::values())->toEqualCanonicalizing(['student', 'moderator', 'program_head', 'dean', 'sao', 'super_admin']);
});

it('UserRole enum has correct labels', function () {
    expect(UserRole::Student->label())->toBe('Student');
    expect(UserRole::Moderator->label())->toBe('Program Moderator');
    expect(UserRole::ProgramHead->label())->toBe('Program Head');
    expect(UserRole::Dean->label())->toBe('College Dean');
    expect(UserRole::Sao->label())->toBe('Administrator');
    expect(UserRole::SuperAdmin->label())->toBe('System Administrator');
});

it('UserRole enum has correct panel classes', function () {
    expect(UserRole::Student->panelClass())->toBe('');
    expect(UserRole::Moderator->panelClass())->toBe('');
    expect(UserRole::ProgramHead->panelClass())->toBe('panel-program-head');
    expect(UserRole::Dean->panelClass())->toBe('panel-dean');
    expect(UserRole::Sao->panelClass())->toBe('panel-sao');
    expect(UserRole::SuperAdmin->panelClass())->toBe('panel-super');
});

it('UserRole enum has correct inherited roles', function () {
    expect(UserRole::SuperAdmin->inheritedRoles())->toBe(['super_admin', 'sao', 'dean', 'program_head', 'moderator']);
    expect(UserRole::Sao->inheritedRoles())->toBe(['sao', 'dean', 'program_head', 'moderator']);
    expect(UserRole::Dean->inheritedRoles())->toBe(['dean', 'program_head', 'moderator']);
    expect(UserRole::ProgramHead->inheritedRoles())->toBe(['program_head', 'moderator']);
    expect(UserRole::Moderator->inheritedRoles())->toBe(['moderator']);
    expect(UserRole::Student->inheritedRoles())->toBe(['student']);
});

it('isSchoolRole excludes super_admin', function () {
    expect(UserRole::Student->isSchoolRole())->toBeTrue();
    expect(UserRole::Moderator->isSchoolRole())->toBeTrue();
    expect(UserRole::ProgramHead->isSchoolRole())->toBeTrue();
    expect(UserRole::Dean->isSchoolRole())->toBeTrue();
    expect(UserRole::Sao->isSchoolRole())->toBeTrue();
    expect(UserRole::SuperAdmin->isSchoolRole())->toBeFalse();
});
