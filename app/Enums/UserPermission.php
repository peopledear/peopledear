<?php

declare(strict_types=1);

namespace App\Enums;

enum UserPermission: string
{
    case ProfileView = 'profile:view';
    case ProfileEdit = 'profile:edit';
    case TimeOffTypeManage = 'time-off-type:manage';
    case TimeOffTypeView = 'time-off-type:view';
    case TimeOffTypeCreate = 'time-off-type:create';
    case TimeOffTypeEdit = 'time-off-type:edit';
    case TimeOffTypeDelete = 'time-off-type:delete';
    case TimeOffTypeActivate = 'time-off-type:activate';
    case TimeOffTypeDeactivate = 'time-off-type:deactivate';

}
