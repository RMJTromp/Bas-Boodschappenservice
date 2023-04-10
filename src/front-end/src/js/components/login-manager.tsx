// login-manager.ts
class LoginManager {
    isLoggedIn: boolean;
    loginTimeout: NodeJS.Timeout | null;

    constructor() {
        this.isLoggedIn = false;
        this.loginTimeout = null;
    }

    login() {
        this.isLoggedIn = true;
        if (this.loginTimeout) clearTimeout(this.loginTimeout);
        this.loginTimeout = setTimeout(() => this.logout(), 8 * 60 * 60 * 1000);
    }

    logout() {
        this.isLoggedIn = false;
        if (this.loginTimeout) clearTimeout(this.loginTimeout);
    }
}

export const loginManager = new LoginManager();
