import login from "./login";
import logout from "./logout";

const auth = {
    login: Object.assign(login, login),
    logout: Object.assign(logout, logout),
};

export default auth;
