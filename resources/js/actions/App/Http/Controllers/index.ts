import SessionController from './SessionController'
import OrganizationOverviewController from './OrganizationOverviewController'
import OrganizationController from './OrganizationController'
import OfficeController from './OfficeController'
import UserController from './UserController'
import UserProfileController from './UserProfileController'
import UserPasswordController from './UserPasswordController'
import UserTwoFactorAuthenticationController from './UserTwoFactorAuthenticationController'
import UserEmailResetNotification from './UserEmailResetNotification'
import UserEmailVerificationNotificationController from './UserEmailVerificationNotificationController'
import UserEmailVerification from './UserEmailVerification'

const Controllers = {
    SessionController: Object.assign(SessionController, SessionController),
    OrganizationOverviewController: Object.assign(OrganizationOverviewController, OrganizationOverviewController),
    OrganizationController: Object.assign(OrganizationController, OrganizationController),
    OfficeController: Object.assign(OfficeController, OfficeController),
    UserController: Object.assign(UserController, UserController),
    UserProfileController: Object.assign(UserProfileController, UserProfileController),
    UserPasswordController: Object.assign(UserPasswordController, UserPasswordController),
    UserTwoFactorAuthenticationController: Object.assign(UserTwoFactorAuthenticationController, UserTwoFactorAuthenticationController),
    UserEmailResetNotification: Object.assign(UserEmailResetNotification, UserEmailResetNotification),
    UserEmailVerificationNotificationController: Object.assign(UserEmailVerificationNotificationController, UserEmailVerificationNotificationController),
    UserEmailVerification: Object.assign(UserEmailVerification, UserEmailVerification),
}

export default Controllers