<?php

use App\Models\Feedback;
use App\Models\User;
use Database\Seeders\SeaitCollegesSeeder;
use Database\Seeders\SeaitProgramsSeeder;
use Database\Seeders\SeaitSchoolSeeder;
use Database\Seeders\SeaitSubjectsSeeder;

beforeEach(function () {
    $this->seed(SeaitSchoolSeeder::class);
    $this->seed(SeaitCollegesSeeder::class);
    $this->seed(SeaitProgramsSeeder::class);
    $this->seed(SeaitSubjectsSeeder::class);
});

it('renders the feedback form for authenticated users', function () {
    $user = User::factory()->onboarded()->create();

    $this->actingAs($user)
        ->get(route('feedback.create'))
        ->assertOk()
        ->assertSee('Feedback');
});

it('stores feedback as a bug report', function () {
    $user = User::factory()->onboarded()->create();

    $this->actingAs($user)
        ->post(route('feedback.store'), [
            'type' => 'bug',
            'body' => 'Something is broken on the resources page.',
        ])
        ->assertRedirect(route('feedback.create'));

    $this->assertDatabaseHas('feedback', [
        'user_id' => $user->id,
        'type' => 'bug',
        'body' => 'Something is broken on the resources page.',
    ]);
});

it('stores feedback as a feature request', function () {
    $user = User::factory()->onboarded()->create();

    $this->actingAs($user)
        ->post(route('feedback.store'), [
            'type' => 'feature',
            'body' => 'Please add dark mode to the dashboard.',
        ])
        ->assertRedirect(route('feedback.create'));

    $feedback = Feedback::latest()->first();
    expect($feedback)->not->toBeNull();
    expect($feedback->type)->toBe('feature');
});

it('defaults type to feedback when no type is provided', function () {
    $user = User::factory()->onboarded()->create();

    $this->actingAs($user)
        ->post(route('feedback.store'), [
            'body' => 'Great app overall!',
            'type' => 'feedback',
        ])
        ->assertRedirect(route('feedback.create'));

    $feedback = Feedback::latest()->first();
    expect($feedback->type)->toBe('feedback');
});

it('rejects feedback shorter than 5 characters', function () {
    $user = User::factory()->onboarded()->create();

    $this->actingAs($user)
        ->post(route('feedback.store'), [
            'type' => 'bug',
            'body' => 'Hi',
        ])
        ->assertSessionHasErrors(['body']);
});

it('rejects feedback longer than 2000 characters', function () {
    $user = User::factory()->onboarded()->create();

    $this->actingAs($user)
        ->post(route('feedback.store'), [
            'type' => 'bug',
            'body' => str_repeat('a', 2001),
        ])
        ->assertSessionHasErrors(['body']);
});

it('flashes a thank-you message on success', function () {
    $user = User::factory()->onboarded()->create();

    $this->actingAs($user)
        ->post(route('feedback.store'), [
            'type' => 'praise',
            'body' => 'Awesome work on the chat feature!',
        ])
        ->assertSessionHas('status', "Thank you for your feedback! We'll review it shortly.");
});

it('blocks unauthenticated users from the feedback form', function () {
    $this->get(route('feedback.create'))->assertRedirect('/login');
});

it('blocks unauthenticated users from submitting feedback', function () {
    $this->post(route('feedback.store'), [
        'type' => 'bug',
        'body' => 'Unauthenticated bug report.',
    ])->assertRedirect('/login');
});
