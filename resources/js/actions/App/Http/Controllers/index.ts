import DashboardController from "./DashboardController";
import Auth from "./Auth";

const Controllers = {
    DashboardController: Object.assign(
        DashboardController,
        DashboardController,
    ),
    Auth: Object.assign(Auth, Auth),
};

export default Controllers;
