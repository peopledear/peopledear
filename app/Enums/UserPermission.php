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

    case TimeOffRequestManage = 'time-off-request:manage';
    case TimeOffRequestView = 'time-off-request:view';
    case TimeOffRequestCreate = 'time-off-request:create';
    case TimeOffRequestEdit = 'time-off-request:edit';
    case TimeOffRequestDelete = 'time-off-request:delete';
    case TimeOffRequestApprove = 'time-off-request:approve';
    case TimeOffRequestReject = 'time-off-request:reject';

    case OfficeManage = 'office:manage';
    case OfficeCreate = 'office:create';
    case OfficeEdit = 'office:edit';
    case OfficeDelete = 'office:delete';

    case OrganizationManage = 'organization:manage';
    case OrganizationView = 'organization:view';
    case OrganizationCreate = 'organization:create';
    case OrganizationEdit = 'organization:edit';
    case OrganizationDelete = 'organization:delete';

}
