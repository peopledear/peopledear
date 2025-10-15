import LoginController from "./LoginController";
import LogoutController from "./LogoutController";

const Auth = {
    LoginController: Object.assign(LoginController, LoginController),
    LogoutController: Object.assign(LogoutController, LogoutController),
};

export default Auth;
