import Admin from "./Admin";
import Auth from "./Auth";
import DashboardController from "./DashboardController";
import Profile from "./Profile";

const Controllers = {
    DashboardController: Object.assign(
        DashboardController,
        DashboardController,
    ),
    Profile: Object.assign(Profile, Profile),
    Admin: Object.assign(Admin, Admin),
    Auth: Object.assign(Auth, Auth),
};

export default Controllers;
