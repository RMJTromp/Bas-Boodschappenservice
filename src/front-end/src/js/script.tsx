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

    for (const {path, Page} of Object.values(routers) as Router[]) {
        if(Array.isArray(path) ? path.some(p => p.test(url.pathname)) : path.test(url.pathname)) {
            main.replaceWith(<Page url={url}/> ?? <main/>);
            break;
        }
    }

    if (!loginManager.isLoggedIn && !/^\/login$/i.test(url.pathname) && !/^\/register$/i.test(url.pathname)) {
        window.history.pushState({}, null, "/login");
        return;
    }

    window.build();

    // if(url.pathname === "/leveranciers") {
    //     main.append(<Leveranciers/>);
    // } else if(url.pathname === "/artikelen") {
    //     main.append(<Artikels/>);
    // } else if(url.pathname === "/klanten") {
    //     main.append(<Klanten/>);
    // } else if(/^\/leverancier\/\d+$/.test(url.pathname)) {
    //     fetch(`http://api.boodschappenservice.loc/leverancier/${url.pathname.split("/")[2]}`)
    //         .then(res => res.json())
    //         .then(res => main.append(...<Leverancier leverancier={res.response}/>));
    // }
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
