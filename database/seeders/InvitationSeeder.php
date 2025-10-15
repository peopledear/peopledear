<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Invitation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

final class InvitationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::factory()->admin()->create();
        $roles = Role::query()->get();

        // Create 5 pending invitations with different roles
        foreach ($roles as $role) {
            Invitation::factory()
                ->pending()
                ->create([
                    'role_id' => $role->id,
                    'invited_by' => $admin->id,
                ]);
        }

        // Create 2 more pending invitations
        Invitation::factory()
            ->count(2)
            ->pending()
            ->create([
                'invited_by' => $admin->id,
                'role_id' => $roles->random()->id,
            ]);

        // Create 2 accepted invitations
        Invitation::factory()
            ->count(2)
            ->accepted()
            ->create([
                'invited_by' => $admin->id,
                'role_id' => $roles->random()->id,
            ]);

        // Create 1 expired invitation
        Invitation::factory()
            ->expired()
            ->create([
                'invited_by' => $admin->id,
                'role_id' => $roles->random()->id,
            ]);
    }
}
