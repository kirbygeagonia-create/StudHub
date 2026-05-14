<?php

use App\Domain\Identity\Rules\AllowedSchoolEmailDomain;
use Illuminate\Support\Facades\Validator;

it('accepts emails from configured domains', function () {
    config()->set('studhub.allowed_email_domains', 'seait.edu.ph,students.seait.edu.ph');

    $rule = new AllowedSchoolEmailDomain;

    $validator = Validator::make(
        ['email' => 'student@students.seait.edu.ph'],
        ['email' => ['email', $rule]],
    );

    expect($validator->fails())->toBeFalse();
});

it('rejects emails outside the allow-list', function () {
    config()->set('studhub.allowed_email_domains', 'seait.edu.ph');

    $rule = new AllowedSchoolEmailDomain;

    $validator = Validator::make(
        ['email' => 'someone@gmail.com'],
        ['email' => ['email', $rule]],
    );

    expect($validator->fails())->toBeTrue();
});

it('treats an empty allow-list as permissive', function () {
    config()->set('studhub.allowed_email_domains', '');

    $rule = new AllowedSchoolEmailDomain;

    $validator = Validator::make(
        ['email' => 'anyone@example.com'],
        ['email' => ['email', $rule]],
    );

    expect($validator->fails())->toBeFalse();
});

it('matches domains case-insensitively', function () {
    config()->set('studhub.allowed_email_domains', 'seait.edu.ph');

    $rule = new AllowedSchoolEmailDomain;

    $validator = Validator::make(
        ['email' => 'mixed@SEAIT.EDU.ph'],
        ['email' => ['email', $rule]],
    );

    expect($validator->fails())->toBeFalse();
});
