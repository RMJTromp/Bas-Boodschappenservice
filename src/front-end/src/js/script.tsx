import { h } from "dom-chef";
import Sidebar from "./components/sidebar";
import "./utilities/windowBuild";
import {Leveranciers} from "./components/leveranciers";
import {Artikels} from "./components/artikels";
const logo = require("../img/logo.png");


function build() {
    const url = new URL(window.location.href);

    const main = document.querySelector("main");
    main.innerHTML = "";
    if(url.pathname === "/leveranciers") {
        main.append(<Leveranciers/>);
    } else if(url.pathname === "/artikels") {
        main.append(<Artikels/>);
    }

    console.log("buidling")
    window.dispatchEvent(new CustomEvent("build"));
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
