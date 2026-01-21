import { InertiaLinkProps } from "@inertiajs/react";
import { LucideIcon } from "lucide-react";
import {
    RequestStatusEnum,
    TimeOffTypeEnum,
    TimeOffTypeStatus,
    TimeOffUnitEnum,
} from "./enums";

export {
    RequestStatusEnum,
    TimeOffTypeEnum,
    TimeOffTypeStatus,
    TimeOffUnitEnum,
};

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavGroup {
    title: string;
    items: NavItem[];
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps["href"]>;
    icon?: LucideIcon | null;
    isActive?: boolean;
    show?: boolean;
    target?: string;
}

export interface SharedData {
    name: string;
    auth: Auth;
    sidebarOpen: boolean;
    show: {
        employeeLink: boolean;
        orgLink?: boolean;
    };
    quote?: {
        message: string;
        author: string;
    };

    previousPath?: string;

    [key: string]: unknown;
}

export interface TenantedSharedData extends SharedData {
    tenant: Organization;
}

export interface User {
    id: string;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    two_factor_enabled?: boolean;
    created_at: string;
    updated_at: string;

    [key: string]: unknown; // This allows for additional properties...
}

export interface Employee {
    id: string;
    name: string;
    email: string;
    phone?: string;
    job_title?: string;
    hire_date?: string;
    status?: "active" | "inactive" | "on_leave";
    organization?: Organization;
    user?: User;
}

export interface Organization {
    id: string;
    name: string;
    identifier: string;
    resourceKey: string;
    vatNumber?: string;
    ssn?: string;
    phone?: string;
    createdAt: string;
    updatedAt: string;
}

export interface RequestStatus {
    status: RequestStatusEnum;
    label: string;
    color: string;
    icon: string;
}

export interface TimeOffType {
    id: string;
    created_at: string;
    updated_at: string;
    organizationId: string;
    organization: Organization;
    fallbackApprovalRoleId: number;
    name: string;
    description: string;
    isSystem: boolean;
    allowedUnits: number[];
    icon: Icon;
    color: string;
    status: TimeOffTypeStatus;
    requiresApproval: boolean;
}

export interface TimeOffUnit {
    value: number;
    label: string;
}

export interface BalanceType {
    value: number;
    label: string;
}

export type EnumOptions = Record<number, string>;

export interface Period {
    id: string;
    organizationId: number;
    start: string;
    end: string;
    year: number;
}

export interface Icon {
    value: string;
    icon: string;
    name: string;
    label: string;
}

export interface TimeOffRequest {
    id: string;
    organizationId: number;
    employeeId: number;
    period: Period;
    type: TimeOffType;
    status: RequestStatus;
    startDate: string;
    endDate: string | null;
    isHalfDay: boolean;
    createdAt: string;
    updatedAt: string;
}

export interface VacationBalance {
    year: number;
    fromLastYear: string;
    accrued: string;
    taken: string;
    remaining: string;
    lastYearBalance: string;
    yearBalance: string;
}

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface PaginatedResponse<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    first_page_url: string;
    last_page_url: string;
    next_page_url: string | null;
    prev_page_url: string | null;
    links: PaginationLink[];
}
