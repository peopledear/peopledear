<?php

declare(strict_types=1);

namespace App\Enums;

enum UserPermission: string
{
    case TimeOffTypeManage = 'time-off-type:manage';
    case TimeOffTypeView = 'time-off-type:view';
    case TimeOffTypeCreate = 'time-off-type:create';
    case TimeOffTypeUpdate = 'time-off-type:update';
    case TimeOffTypeDelete = 'time-off-type:delete';
    case TimeOffTypeActivate = 'time-off-type:activate';
    case TimeOffTypeDeactivate = 'time-off-type:deactivate';

}
