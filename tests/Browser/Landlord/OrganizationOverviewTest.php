<?php

declare(strict_types=1);

use function Pest\Laravel\actingAs;

test('admin layout renders for people manager role', function (): void {
    actingAs($this->peopleManager);

    $page = visit(route(
        name: 'org.overview',
        absolute: false
    ));

    $page->assertSee('Overview')
        ->assertNoJavascriptErrors();
});

test('admin layout renders for owner role', function (): void {
    actingAs($this->owner);

    $page = visit(route(
        name: 'org.overview',
        absolute: false
    ));

    $page->assertSee('Overview')
        ->assertNoJavascriptErrors();
});

test('admin layout redirects employee role', function (): void {
    actingAs($this->employee);

    $page = visit(route(
        name: 'org.overview',
        absolute: false
    ));

    $page->assertSee('403')
        ->assertNoJavascriptErrors();
});

test('admin navigation menu displays correct items', function (): void {
    actingAs($this->peopleManager);

    $page = visit(route(
        name: 'org.overview',
        absolute: false
    ));

    $page->assertSee('Overview')
        ->assertSee('Settings')
        ->assertNoJavascriptErrors();
});

test('mobile navigation works on small screens', function (): void {
    actingAs($this->owner);

    $page = visit(route(
        name: 'org.overview',
        absolute: false
    ))
        ->resize(375, 667);

    $page->assertNoJavascriptErrors();
});
