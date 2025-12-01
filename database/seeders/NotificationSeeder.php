<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

final class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::query()->get();

        $users->each(function (User $user): void {

            Notification::factory()
                ->count(10)
                ->for($user, 'notifiable')
                ->createQuietly();

        });
    }
}
