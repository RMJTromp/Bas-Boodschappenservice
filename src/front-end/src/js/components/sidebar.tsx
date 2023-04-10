import { h } from "dom-chef";

export function Link({link, icon = "blank", text, badge = false, sub = false}) {
    return <a href={link} class={sub ? "active sub-link" : ""}>
        <div className="target">
            <i className={`codicon codicon-${sub ? "arrow-small-right" : icon}`}></i>
            {text}
        </div>
        {badge ? <span className="badge"/> : undefined}
    </a>;
}

Link.defaultProps = {
    link: undefined,
    text: undefined,
    icon: "blank",
    badge: false,
    sub: false,
}

export default function Sidebar() {
    const sidebar : HTMLDivElement = <aside>
        <Link link={"/"} icon={"home"} text={"Home"}/>
        <Link link={"/leveranciers"} icon={"rocket"} text={"Leveranciers"} badge={true}/>
        <Link link={"/klanten"} icon={"organization"} text={"Klanten"} badge={true}/>
        <Link link={"/artikelen"} icon={"package"} text={"Artikels"} badge={true}/>
    </aside>;

    window.onupdate = async () => {
        const url = new URL(window.location.href);

        const counts = (await ((await fetch("http://api.boodschappenservice.loc/count")).json())).response;
        sidebar.querySelectorAll("a:not(.sub-link)").forEach((a : HTMLAnchorElement) => {
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