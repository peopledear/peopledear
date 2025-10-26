<?php

declare(strict_types=1);

use App\Enums\SessionKey;

test('has current organization case', function (): void {
    expect(SessionKey::CurrentOrganization->value)->toBe('current_organization');
});

test('all cases have string values', function (): void {
    foreach (SessionKey::cases() as $case) {
        expect($case->value)->toBeString();
    }
});
