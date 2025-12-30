<?php

declare(strict_types=1);

use App\Http\Middleware\HandleInertiaRequests;
use App\Models\User;
use Illuminate\Http\Request;

beforeEach(function (): void {

    $this->middleware = resolve(HandleInertiaRequests::class);

});

it('shares app name from config', function (): void {

    $request = Request::create('/', 'GET');

    $shared = $this->middleware->share($request);

    expect($shared)->toHaveKey('name')
        ->and($shared['name'])->toBe(config('app.name'));
});

it('shares null user when guest', function (): void {

    $request = Request::create('/', 'GET');

    $shared = $this->middleware->share($request);

    expect($shared)->toHaveKey('auth')
        ->and($shared['auth'])->toHaveKey('user')
        ->and($shared['auth']['user'])->toBeNull();
});

it('shares authenticated user data', function (): void {
    $user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    $request = Request::create('/', 'GET');
    $request->setUserResolver(fn () => $user);

    $shared = $this->middleware->share($request);

    expect($shared['auth']['user'])->not->toBeNull()
        ->and($shared['auth']['user']->id)->toBe($user->id)
        ->and($shared['auth']['user']->name)->toBe('Test User')
        ->and($shared['auth']['user']->email)->toBe('test@example.com');
});

it('defaults sidebarOpen to true when no cookie', function (): void {

    $request = Request::create('/', 'GET');

    $shared = $this->middleware->share($request);

    expect($shared)->toHaveKey('sidebarOpen')
        ->and($shared['sidebarOpen'])->toBeTrue();
});

it('sets sidebarOpen to true when cookie is true', function (): void {

    $request = Request::create('/', 'GET');
    $request->cookies->set('sidebar_state', 'true');

    $shared = $this->middleware->share($request);

    expect($shared['sidebarOpen'])->toBeTrue();
});

it('sets sidebarOpen to false when cookie is false', function (): void {

    $request = Request::create('/', 'GET');
    $request->cookies->set('sidebar_state', 'false');

    $shared = $this->middleware->share($request);

    expect($shared['sidebarOpen'])->toBeFalse();
});

it('includes parent shared data', function (): void {

    $request = Request::create('/', 'GET');

    $shared = $this->middleware->share($request);

    expect($shared)->toHaveKey('errors');
});
