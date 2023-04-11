import { Router } from "../types";
import { Modal as BaseModal } from "../components/modal";
import { h } from "dom-chef";
import { loginManager } from "../components/login-manager";
import syncFetch from "../utilities/sync-fetch";

export function RegisterModal(props: { callback: () => void, [key: string]: any }) {
    let usernameInput, passwordInput, emailInput;
    const self = (
        <BaseModal closeable={false}>
            <header>
                <h2>Register</h2>
            </header>
            <main>
                <label>Username
                    {usernameInput = <input type="text" />}
                </label>
                <label>Password
                    {passwordInput = <input type="password" />}
                </label>
                <label>Email
                    {emailInput = <input type="email" />}
                </label>
            </main>
            <footer>
                <button onClick={() => window.history.pushState({}, null, "/login")}>Login</button>
                <button className={"primary"} onClick={function () {
                    this.disabled = true;

                    const body = {
                        username: usernameInput.value,
                        password: passwordInput.value,
                        email: emailInput.value,
                    };

                    fetch(`http://api.boodschappenservice.loc/register`, {
                        method: "POST",
                        body: JSON.stringify(body),
                        headers: {
                            "Content-Type": "application/json"
                        }
                    }).then(async (res) => {
                        if (res.status === 200) {
                            loginManager.login();
                            self.open = false;
                            props.callback();
                        } else {
                            const response = await res.json();
                            self.querySelector("main > div.alert")?.remove();
                            self.querySelector("main").prepend(
                                <div className="alert">
                                    <p>{response.meta.exception ?? response.meta.status.message}</p>
                                </div> as HTMLDivElement
                            )
                            this.disabled = false;
                        }
                    })
                }}>Register</button>
            </footer>
        </BaseModal>
    );
    self.closeable = false;
    return self;
}

export default {
    path: /^\/register$/i,
    Page: () => {
        return <main>
            <RegisterModal callback={() => { /* Update after registration */ }} />
        </main>;
    }
} as Router;
