import {Klant, Router} from "../types";
import {Modal as BaseModal} from "../components/modal";
import Codicon from "../components/codicon";
import {h} from "dom-chef";
import syncFetch from "../utilities/sync-fetch";

export function Modal(props : {callback: (klant: Klant) => void, klant?: Klant, [key : string] : any}) {
    const klant = props.klant ?? null;
    let naamInput, adresInput, postcodeInput, plaatsInput, emailInput;
    const self = <BaseModal>
        <header>
            <h2>{klant ? "Wijzig Klant" : "Nieuwe Klant"}</h2>
        </header>
        <main>
            <label>Naam
                {naamInput = <input type="text" value={klant?.naam ?? ""}/>}
            </label>
            <label>E-mail
                {emailInput = <input type="email" value={klant?.email ?? ""}/>}
            </label>
            <div style={{display: "grid", gridTemplateColumns: "65% 31%", gap: "1rem"}}>
                <label>Adres
                    {adresInput = <input type="text" value={klant?.adres ?? ""}/>}
                </label>
                <label>Postcode
                    {postcodeInput = <input type="text" style={{textTransform: "uppercase"}} value={klant?.postcode ?? ""}/>}
                </label>
            </div>
            <label>Plaats
                {plaatsInput = <input type="text" value={klant?.woonplaats ?? ""}/>}
            </label>
        </main>
        <footer>
            <button onClick={() => self.open = false}>Annuleren</button>
            <button className={"primary"} onClick={function () {
                this.disabled = true;

                const body = {
                    naam: naamInput.value,
                    adres: adresInput.value,
                    postcode: postcodeInput.value,
                    woonplaats: plaatsInput.value,
                    email: emailInput.value,
                };

                const promise = klant
                    ? fetch(`http://api.boodschappenservice.loc/klant/${klant.id}`, {
                        method: "PATCH",
                        body: JSON.stringify(body),
                        headers: {
                            "Content-Type": "application/json"
                        }
                    })
                    : fetch(`http://api.boodschappenservice.loc/klant`, {
                        method: "POST",
                        body: JSON.stringify(body),
                        headers: {
                            "Content-Type": "application/json"
                        }
                    });

                promise.then(async (res) => {
                    if (res.status === 200) {
                        self.open = false;
                        props.callback(await res.json());
                    } else {
                        const response = await res.json();
                        self.querySelector("main > div.alert")?.remove();
                        self.querySelector("main").prepend(
                            <div className="alert">
                                <Codicon name={"alert"}/>
                                <p>{response.meta.exception ?? response.meta.status.message}</p>
                            </div> as HTMLDivElement
                        )
                        this.disabled = false;
                    }
                })
            }}>Opslaan</button>
        </footer>
    </BaseModal>;
    return self;
}

function Entry(props : {klant : Klant, callback: (klant: Klant) => void, [key : string] : any}) {
    const klant = props.klant;
    const self = (
        <div className="klant">
            <div className="details">
                <p>{klant.naam}<br/><a href={`mailto:${klant.email}`} style={{textTransform: "lowercase"}}>{klant.email}</a></p>
                <p>{klant.adres}, {klant.postcode}<br/>{klant.woonplaats}</p>
            </div>
            <div className="controls">
                <a onClick={() => {
                    document.body.append(<Modal klant={klant} callback={props.callback}/>);
                }}><Codicon name={"pencil"}/></a>
                <a onClick={() => {
                    fetch(`http://api.boodschappenservice.loc/klant/${klant.id}`, {method: "DELETE"})
                        .then(res => {
                            if(res.status === 200) {
                                self.remove();
                                window.update();
                            }
                        })
                }}><Codicon name={"trash"}/></a>
            </div>
        </div>
    )
    return self;
}

export default {
    path: /^\/klanten$/i,
    Page: ({url}) => {
        let previousPage, pagination, nextPage, table, newButton, generateButton, searchInput;
        const self : HTMLDivElement = (
            <main>
                <section id="klanten">
                    <div className="container">
                        <div className="title">
                            <h1>Klanten</h1>
                            <div class={"controls"}>
                                {generateButton = <button>Genereren</button>}
                                {newButton = <button>Nieuw</button>}
                            </div>
                        </div>
                        <div className="card">
                            <div className="controls">
                                {searchInput = <input type="text" placeholder="Zoeken..." style={{width: "300px"}}/>}
                                <div className="pagination">
                                    {previousPage = <a disabled={true}><Codicon name={"chevron-left"}/></a>}
                                    {pagination = <p>1 / ?</p>}
                                    {nextPage = <a disabled={true}><Codicon name={"chevron-right"} /></a>}
                                </div>
                            </div>
                            {table = <div className="table"></div>}
                        </div>
                    </div>
                </section>
            </main>
        );

        generateButton.onclick = () => {
            fetch("http://api.boodschappenservice.loc/klant?random&amount=100", {method: "POST"})
                .then(res => {
                    if(res.status === 200) {
                        window.update();
                        getKlanten();
                    }
                })
        }

        // check searchInput for changes after no action for 500ms
        let searchTimeout;
        const doneTyping = () => {
            const searchValue = searchInput.value.trim();

            const url = new URL(window.location.href);
            if(searchValue.length > 0) url.searchParams.set("search", searchValue);
            else url.searchParams.delete("search");
            url.searchParams.delete("page");
            window.history.replaceState({}, "", url.href);

            getKlanten(searchValue);
        }
        const inputEvent = () => {
            if(searchTimeout) clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => doneTyping(), 200);
        };
        searchInput.oninput = inputEvent;
        searchInput.onchange = inputEvent;
        searchInput.onkeyup = inputEvent;
        searchInput.onkeydown = inputEvent;
        searchInput.value = url.searchParams.get("search") || "";

        newButton.onclick = () => document.body.append(<Modal callback={() => {
            getKlanten();
            window.update();
        }}/>);

        const getKlanten = (search = "") => {
            const url = new URL(window.location.href);
            const page = Math.max(parseInt(url.searchParams.get("page")) || 1, 1);

            const res = syncFetch(`http://api.boodschappenservice.loc/klanten?limit=100&offset=${(page - 1) * 100}&search=${search}`).json();

            const maxPage = Math.ceil(res.meta.results.total / 100);

            // previous page button
            previousPage.href = (() => {
                let _url = new URL(window.location.href);
                _url.searchParams.set("page", (page-1).toString());
                return page !== 1 ? _url.href : "";
            })();
            if(page <= 1) previousPage.setAttribute("disabled", "true");
            else previousPage.removeAttribute("disabled");

            // next page button
            nextPage.href = (() => {
                let _url = new URL(window.location.href);
                _url.searchParams.set("page", (page+1).toString());
                return page !== maxPage ? _url.href : "";
            })();
            if(page >= maxPage) nextPage.setAttribute("disabled", "true");
            else nextPage.removeAttribute("disabled");

            pagination.innerText = `${page} / ${maxPage}`;

            table.innerHTML = "";
            table.append(...res.response.map((klant : Klant) => <Entry klant={klant} callback={() => getKlanten(search)}/>));
        }

        getKlanten(searchInput.value.trim());

        return self;
    }
} as Router;