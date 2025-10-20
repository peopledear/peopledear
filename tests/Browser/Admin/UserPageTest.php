<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\User;

beforeEach(function (): void {
    $adminRole = Role::query()->firstOrCreate(
        ['name' => 'admin'],
        ['display_name' => 'Administrator', 'description' => 'Full system access']
    );

    $this->admin = User::factory()->create([
        'role_id' => $adminRole->id,
        'is_active' => true,
    ]);
});

test('may show the users page', function (): void {
    $this->actingAs($this->admin);

    $page = visit(route('admin.users.index'));

    $page->assertSee('Members');
});
