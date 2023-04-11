import { h } from "dom-chef";
import Sidebar from "./components/sidebar";
import { loginManager } from "./components/login-manager";
import "./utilities/windowBuild";
const logo = require("../img/logo.png");
import * as routers from "./routes";
import {Router} from "./types";

function build() {
    const url = new URL(window.location.href);

    document.querySelector("aside")
        .querySelectorAll("a.sub-link")
        .forEach(a => a.remove());

    const main = document.querySelector("main");
    main.innerHTML = "";


    // if (!loginManager.isLoggedIn && !/^\/login$/i.test(url.pathname) && !/^\/register$/i.test(url.pathname)) {
    //     window.history.pushState({}, null, "/login");
    //     window.build();
    //     return;
    // }

    for (const {path, Page} of Object.values(routers) as Router[]) {
        if(Array.isArray(path) ? path.some(p => p.test(url.pathname)) : path.test(url.pathname)) {
            main.replaceWith(<Page url={url}/> ?? <main/>);
            break;
        }
    }

    window.build();
}


window.addEventListener('DOMContentLoaded', () => {
    document.body.append(
        <header>
            <img src={logo} alt="BAS logo" />
        </header>,
        <Sidebar/>,
        <main></main>
    );

    window.history.pushState = new Proxy(window.history.pushState, {
        apply: async (target, thisArg, argArray) => {
            target.apply(thisArg, argArray);
            build();
        },
    });

    window.onclick = (e) => {
        if(e.target.matches("a[href]")) {
            if(e.target.host === window.location.host) {
                e.preventDefault();
                if(e.target.href !== window.location.href && (e.target as HTMLAnchorElement).hasAttribute("disabled") === false)
                    window.history.pushState({}, null, e.target.href);
            }
        } else {
            let target : HTMLAnchorElement
            if((target = e.target.closest("a[href]")) && target.host === window.location.host) {
                e.preventDefault();
                if(target.href !== window.location.href && target.hasAttribute("disabled") === false)
                    window.history.pushState({}, null, target.href);
            }
        }
    }

    window.onpopstate = () => build();

    build();
});
