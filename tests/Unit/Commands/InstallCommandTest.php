<?php

declare(strict_types=1);

test('command has correct signature', function (): void {
    $this->artisan('app:install')
        ->assertSuccessful();
});

test('command returns success exit code', function (): void {
    $this->artisan('app:install')
        ->assertExitCode(0);
});
