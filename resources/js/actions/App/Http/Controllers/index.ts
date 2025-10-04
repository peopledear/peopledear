import Auth from "./Auth";
import DashboardController from "./DashboardController";

const Controllers = {
    DashboardController: Object.assign(
        DashboardController,
        DashboardController,
    ),
    Auth: Object.assign(Auth, Auth),
};

export default Controllers;
