<?php

declare(strict_types=1);

use App\Enums\SyncJobType;
use App\Enums\SyncLogStatus;
use App\Models\Organization;
use App\Models\SyncLog;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

test('sync log has organization relationship', function (): void {
    /** @var SyncLog $syncLog */
    $syncLog = SyncLog::factory()->createQuietly();

    expect($syncLog->organization())
        ->toBeInstanceOf(BelongsTo::class);
});

test('sync log organization relationship is properly loaded', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    /** @var SyncLog $syncLog */
    $syncLog = SyncLog::factory()->createQuietly([
        'organization_id' => $organization->id,
    ]);

    $syncLog->load('organization');

    expect($syncLog->organization)
        ->toBeInstanceOf(Organization::class)
        ->and($syncLog->organization->id)
        ->toBe($organization->id);
});

test('sync log job_type is cast to SyncJobType enum', function (): void {
    /** @var SyncLog $syncLog */
    $syncLog = SyncLog::factory()->createQuietly([
        'job_type' => SyncJobType::HolidaySync,
    ]);

    expect($syncLog->job_type)
        ->toBeInstanceOf(SyncJobType::class)
        ->and($syncLog->job_type)
        ->toBe(SyncJobType::HolidaySync)
        ->and($syncLog->job_type->value)
        ->toBe(1);
});

test('sync log status is cast to SyncLogStatus enum', function (): void {
    /** @var SyncLog $syncLog */
    $syncLog = SyncLog::factory()->createQuietly([
        'status' => SyncLogStatus::Success,
    ]);

    expect($syncLog->status)
        ->toBeInstanceOf(SyncLogStatus::class)
        ->and($syncLog->status)
        ->toBe(SyncLogStatus::Success)
        ->and($syncLog->status->value)
        ->toBe(1);
});

test('sync log synced_at is cast to datetime', function (): void {
    /** @var SyncLog $syncLog */
    $syncLog = SyncLog::factory()->createQuietly([
        'synced_at' => '2025-10-26 12:00:00',
    ]);

    expect($syncLog->synced_at)
        ->toBeInstanceOf(Carbon\CarbonInterface::class)
        ->and($syncLog->synced_at->format('Y-m-d H:i:s'))
        ->toBe('2025-10-26 12:00:00');
});

test('sync log metadata is cast to array', function (): void {
    /** @var SyncLog $syncLog */
    $syncLog = SyncLog::factory()->createQuietly([
        'metadata' => [
            'source' => 'api',
            'duration' => 120,
        ],
    ]);

    expect($syncLog->metadata)
        ->toBeArray()
        ->and($syncLog->metadata['source'])
        ->toBe('api')
        ->and($syncLog->metadata['duration'])
        ->toBe(120);
});

test('sync log records_synced_count is cast to integer', function (): void {
    /** @var SyncLog $syncLog */
    $syncLog = SyncLog::factory()->createQuietly([
        'records_synced_count' => 42,
    ]);

    expect($syncLog->records_synced_count)
        ->toBeInt()
        ->and($syncLog->records_synced_count)
        ->toBe(42);
});

test('sync log can be failed with error message', function (): void {
    /** @var SyncLog $syncLog */
    $syncLog = SyncLog::factory()->failed()->createQuietly();

    expect($syncLog->status)
        ->toBe(SyncLogStatus::Failed)
        ->and($syncLog->records_synced_count)
        ->toBe(0)
        ->and($syncLog->error_message)
        ->not->toBeNull();
});

test('sync log can be partial with error message', function (): void {
    /** @var SyncLog $syncLog */
    $syncLog = SyncLog::factory()->partial()->createQuietly();

    expect($syncLog->status)
        ->toBe(SyncLogStatus::Partial)
        ->and($syncLog->records_synced_count)
        ->toBeGreaterThan(0)
        ->and($syncLog->error_message)
        ->not->toBeNull();
});

test('sync log error_message can be null for successful syncs', function (): void {
    /** @var SyncLog $syncLog */
    $syncLog = SyncLog::factory()->createQuietly([
        'status' => SyncLogStatus::Success,
        'error_message' => null,
    ]);

    expect($syncLog->error_message)->toBeNull();
});

test('sync log metadata can be null', function (): void {
    /** @var SyncLog $syncLog */
    $syncLog = SyncLog::factory()->createQuietly([
        'metadata' => null,
    ]);

    expect($syncLog->metadata)->toBeNull();
});

test('sync log does not have timestamps', function (): void {
    /** @var SyncLog $syncLog */
    $syncLog = SyncLog::factory()->createQuietly();

    expect($syncLog->toArray())
        ->not->toHaveKey('created_at')
        ->not->toHaveKey('updated_at');
});

test('to array', function (): void {
    /** @var SyncLog $syncLog */
    $syncLog = SyncLog::factory()
        ->createQuietly()
        ->refresh();

    expect(array_keys($syncLog->toArray()))
        ->toBe([
            'id',
            'synced_at',
            'organization_id',
            'job_type',
            'status',
            'records_synced_count',
            'error_message',
            'metadata',
        ]);
});
