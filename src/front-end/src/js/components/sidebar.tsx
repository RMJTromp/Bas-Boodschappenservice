import { h } from "dom-chef";

export default function Sidebar() {
    const sidebar : HTMLDivElement = <aside>
        <a href="/">
            <div className="target">
                <i className="codicon codicon-home"></i>
                Home
            </div>
        </a>

        <a href="/leveranciers">
            <div className="target">
                <i className="codicon codicon-rocket"></i>
                Leveranciers
            </div>
            <span className="badge"></span>
        </a>

        <a href="/klanten">
            <div className="target">
                <i className="codicon codicon-organization"></i>
                Klanten
            </div>
            <span className="badge"></span>
        </a>

        <a href="/artikels">
            <div className="target">
                <i className="codicon codicon-package"></i>
                Artikels
            </div>
            <span className="badge"></span>
        </a>
    </aside>;

    window.build = async () => {
        const url = new URL(window.location.href);

        const counts = (await ((await fetch("http://api.boodschappenservice.loc/count")).json())).response;
        sidebar.querySelectorAll("a").forEach(a => {
            const target = new URL(a.href);
            if (url.host === target.host && url.pathname === target.pathname) a.classList.add("active");
            else a.classList.remove("active");

            let badge;
            if((badge = a.querySelector("span.badge"))) {
                const total = counts[target.pathname.slice(1, target.pathname.length)] ?? 0;
                if(total === 0) badge.innerText = "";
                else badge.innerText = total.toString();
            }
        });
    };

    return sidebar;
}