<?php

declare(strict_types=1);

use App\Models\Admin;
use Filament\Panel;

test('can access panel returns true', function (): void {
    $admin = Admin::factory()->create();
    $panel = Mockery::mock(Panel::class);

    expect($admin->canAccessPanel($panel))->toBeTrue();
});

test('to array', function (): void {
    $admin = Admin::factory()->create()->refresh();

    expect(array_keys($admin->toArray()))
        ->toBe([
            'id',
            'created_at',
            'updated_at',
            'name',
            'email',
        ]);
});
