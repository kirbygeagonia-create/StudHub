<?php

use Database\Seeders\SeaitSchoolSeeder;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register with a SEAIT email and land on onboarding', function () {
    $this->seed(SeaitSchoolSeeder::class);

    $response = $this->post('/register', [
        'name' => 'Jane Estudyante',
        'email' => 'jane@seait.edu.ph',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('onboarding.show', absolute: false));
});

test('registration rejects email domains outside the allow-list', function () {
    $response = $this->from('/register')->post('/register', [
        'name' => 'Outsider',
        'email' => 'outsider@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertGuest();
    $response->assertRedirect('/register');
    $response->assertSessionHasErrors('email');
});
