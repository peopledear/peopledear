import SessionController from './SessionController'
import OrgOverviewController from './OrgOverviewController'
import OrgController from './OrgController'
import OrgOfficeController from './OrgOfficeController'
import UserController from './UserController'
import UserProfileController from './UserProfileController'
import UserPasswordController from './UserPasswordController'
import UserTwoFactorAuthenticationController from './UserTwoFactorAuthenticationController'
import UserEmailResetNotification from './UserEmailResetNotification'
import UserEmailVerificationNotificationController from './UserEmailVerificationNotificationController'
import UserEmailVerification from './UserEmailVerification'

const Controllers = {
    SessionController: Object.assign(SessionController, SessionController),
    OrgOverviewController: Object.assign(OrgOverviewController, OrgOverviewController),
    OrgController: Object.assign(OrgController, OrgController),
    OrgOfficeController: Object.assign(OrgOfficeController, OrgOfficeController),
    UserController: Object.assign(UserController, UserController),
    UserProfileController: Object.assign(UserProfileController, UserProfileController),
    UserPasswordController: Object.assign(UserPasswordController, UserPasswordController),
    UserTwoFactorAuthenticationController: Object.assign(UserTwoFactorAuthenticationController, UserTwoFactorAuthenticationController),
    UserEmailResetNotification: Object.assign(UserEmailResetNotification, UserEmailResetNotification),
    UserEmailVerificationNotificationController: Object.assign(UserEmailVerificationNotificationController, UserEmailVerificationNotificationController),
    UserEmailVerification: Object.assign(UserEmailVerification, UserEmailVerification),
}

export default Controllers