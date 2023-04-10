import { Router } from "../types";
import { Modal as BaseModal } from "../components/modal";
import { h } from "dom-chef";
import { loginManager } from "../components/login-manager";
import syncFetch from "../utilities/sync-fetch";

export function LoginModal(props: { callback: () => void, [key: string]: any }) {
    let usernameInput, passwordInput;
    const self = (
        <BaseModal>
            <header>
                <h2>Login</h2>
            </header>
            <main>
                <label>Username
                    {usernameInput = <input type="text" />}
                </label>
                <label>Password
                    {passwordInput = <input type="password" />}
                </label>
            </main>
            <footer>
                <button onClick={() => self.open = false}>Cancel</button>
                <button onClick={() => window.history.pushState({}, null, "/register")}>Go to Register</button>
                <button className={"primary"} onClick={function () {
                    this.disabled = true;

                    const body = {
                        username: usernameInput.value,
                        password: passwordInput.value,
                    };

                    fetch(`http://api.boodschappenservice.loc/login`, {
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
                }}>Login</button>
            </footer>
        </BaseModal>
    );
    return self;
}

export default {
    path: /^\/login$/i,
    Page: () => {
        const self: HTMLDivElement = (
            <main>
                <section>
                    <div className="container">
                        <div className="title">
                            <h1>Login</h1>
                        </div>
                        <div className="card">
                            {LoginModal({ callback: () => { /* Update after login */ } })}
                        </div>
                    </div>
                </section>
            </main>
        );

        return self;
    }
} as Router;
