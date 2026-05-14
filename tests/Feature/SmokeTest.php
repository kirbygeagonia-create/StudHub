<?php

it('returns a successful response from the home page', function () {
    $response = $this->get('/');

    expect($response->status())->toBe(200);
});

it('exposes the configured app name', function () {
    expect(config('app.name'))->toBeString()->not->toBeEmpty();
});

it('uses Asia/Manila as the default timezone for the SEAIT pilot', function () {
    expect(config('app.timezone'))->toBe('Asia/Manila');
});
