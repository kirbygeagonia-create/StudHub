<?php

use App\Domain\Moderation\Enums\ReportReason;
use App\Domain\Moderation\Enums\ReportStatus;
use App\Domain\Moderation\Enums\ReportedType;

test('ReportStatus enum has expected cases', function () {
    expect(ReportStatus::cases())->toHaveCount(3);
    expect(ReportStatus::Open->value)->toBe('open');
    expect(ReportStatus::Dismissed->value)->toBe('dismissed');
    expect(ReportStatus::Actioned->value)->toBe('actioned');
});

test('ReportReason enum has expected cases', function () {
    expect(ReportReason::cases())->toHaveCount(5);
    expect(ReportReason::Spam->value)->toBe('spam');
    expect(ReportReason::Harassment->value)->toBe('harassment');
});

test('ReportedType enum has expected cases', function () {
    expect(ReportedType::cases())->toHaveCount(3);
    expect(ReportedType::Message->value)->toBe('message');
    expect(ReportedType::Resource->value)->toBe('resource');
    expect(ReportedType::User->value)->toBe('user');
});