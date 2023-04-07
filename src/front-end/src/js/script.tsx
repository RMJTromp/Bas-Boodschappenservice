import { h } from "dom-chef";

function build() {
    document.body.innerHTML = "";
    const url = new URL(window.location.href);
    if(url.pathname === "/leveranciers") {

    }
}


window.addEventListener('DOMContentLoaded', () => {
    // document.body.append(
    //     <Titlebar/>,
    //     <main>
    //         <section id="nav">
    //             <div class="container">
    //                 <Navbar/>
    //             </div>
    //         </section>
    //     </main>,
    //     <SearchBar/>
    // );

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

                if(e.target.href !== window.location.href)
                    window.history.pushState({}, null, e.target.href);
            }
        }
    }

    window.onpopstate = () => build();

    build();
});
