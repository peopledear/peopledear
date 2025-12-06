<?php

declare(strict_types=1);

namespace App\Actions\TimeOffRequest;

use App\Actions\Approval\CreateApproval;
use App\Data\PeopleDear\TimeOffRequest\CreateTimeOffRequestData;
use App\Enums\PeopleDear\RequestStatus;
use App\Models\Employee;
use App\Models\TimeOffRequest;
use App\Notifications\GeneralNotification;
use App\Registries\TimeOffTypeRegistry;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreateTimeOffRequest
{
    public function __construct(
        private TimeOffTypeRegistry $registry,
        private CreateApproval $createApproval,
    ) {}

    /**
     * @throws Throwable
     */
    public function handle(CreateTimeOffRequestData $data, Employee $employee): TimeOffRequest
    {
        return DB::transaction(function () use ($data, $employee): TimeOffRequest {
            /** @var TimeOffRequest $timeOff */
            $timeOff = TimeOffRequest::query()
                ->create([
                    ...$data->toArray(),
                    'status' => RequestStatus::Pending,
                ]);

            $status = $data->type->isAutomaticApproved()
                ? RequestStatus::Approved
                : RequestStatus::Pending;

            $this->createApproval->handle($timeOff, $data->organizationId, $status);

            if ($data->type->isAutomaticApproved()) {
                $processor = $this->registry->getProcessor($data->type);
                $processor->process($timeOff);
            }

            $endDateText = $data->endDate instanceof CarbonImmutable
                ? ' to '.$data->endDate->toFormattedDateString()
                : '';

            $notification = new GeneralNotification(
                'Time Off Request Submitted',
                sprintf('<strong>%s</strong> has requested a new time off from %s%s.', $employee->name, $data->startDate->toFormattedDateString(), $endDateText)
            );

            $employee->manager?->user?->notify($notification);

            return $timeOff;
        });
    }
}
