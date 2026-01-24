<?php

declare(strict_types=1);

use App\Actions\User\CreateUser;
use App\Data\PeopleDear\CreateUserData;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;

beforeEach(function (): void {
    $this->organization = Organization::factory()->createQuietly();

    $this->action = resolve(CreateUser::class);
});

test('creates user with valid data', function (): void {
    Event::fake([Registered::class]);

    $data = CreateUserData::from([
        'organization_id' => $this->organization->id,
        'name' => 'Test User',
        'email' => 'example@email.com',
        'password' => 'password',
    ]);

    /** @var User $user */
    $user = $this->action->handle($data);

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->name)->toBe('Test User')
        ->and($user->email)->toBe('example@email.com')
        ->and($user->organization_id)->toBe($this->organization->id)
        ->and($user->password)->not->toBe('password');

    Event::assertDispatched(Registered::class);
});
