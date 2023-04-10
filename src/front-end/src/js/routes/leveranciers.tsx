import {h} from "dom-chef";
import {Leverancier, Router} from "../types";
import {Modal as BaseModal} from "../components/modal";
import Codicon from "../components/codicon";
import syncFetch from "../utilities/sync-fetch";

export function Modal(props : {callback: (leverancier: Leverancier) => void, leverancier?: Leverancier, [key : string] : any}) {
    const leverancier = props.leverancier ?? null;
    let naamInput, adresInput, postcodeInput, plaatsInput, contactInput, emailInput;
    const self = <BaseModal>
        <header>
            <h2>{leverancier ? "Wijzig Leverancier" : "Nieuwe Leverancier"}</h2>
        </header>
        <main>
            <h4>Leverancier</h4>
            <label>Naam
                {naamInput = <input type="text" value={leverancier?.naam ?? ""}/>}
            </label>
            <div style={{display: "grid", gridTemplateColumns: "65% 31%", gap: "1rem"}}>
                <label>Adres
                    {adresInput = <input type="text" value={leverancier?.adres ?? ""}/>}
                </label>
                <label>Postcode
                    {postcodeInput = <input type="text" style={{textTransform: "uppercase"}} value={leverancier?.postcode ?? ""}/>}
                </label>
            </div>
            <label>Plaats
                {plaatsInput = <input type="text" value={leverancier?.woonplaats ?? ""}/>}
            </label>

            <h4 style={{marginTop: "15px"}}>Contactpersoon</h4>
            <label>Volledige Naam
                {contactInput = <input type="text" value={leverancier?.contact ?? ""}/>}
            </label>
            <label>E-mail
                {emailInput = <input type="email" value={leverancier?.email ?? ""}/>}
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
                    contact: contactInput.value,
                    email: emailInput.value,
                };

                const promise = leverancier
                    ? fetch(`http://api.boodschappenservice.loc/leverancier/${leverancier.id}`, {
                        method: "PATCH",
                        body: JSON.stringify(body),
                        headers: {
                            "Content-Type": "application/json"
                        }
                    })
                    : fetch(`http://api.boodschappenservice.loc/leverancier`, {
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

export function Entry(props : {leverancier : Leverancier, callback: (leverancier: Leverancier) => void, [key : string] : any}) {
    let leverancier = props.leverancier, self;
    return (self = (
        <div className="leverancier">
            <div className="details">
                <p>{leverancier.naam}</p>
                <p>{leverancier.contact}<br/><a href={`mailto:${leverancier.email}`}
                                                style={{textTransform: "lowercase"}}>{leverancier.email}</a></p>
                <p>{leverancier.adres}, {leverancier.postcode}<br/>{leverancier.woonplaats}</p>
            </div>
            <div className="controls">
                <a onClick={() => {
                    document.body.append(<Modal leverancier={leverancier} callback={props.callback}/>);
                }}><Codicon name={"pencil"}/></a>
                <a onClick={() => {
                    fetch(`http://api.boodschappenservice.loc/leverancier/${leverancier.id}`, {method: "DELETE"})
                        .then(res => {
                            if(res.status === 200) {
                                self.remove();
                                window.update();
                            }
                        })
                }}><Codicon name={"trash"}/></a>
                <a href={`/leverancier/${leverancier.id}`}><Codicon name={"chevron-right"}/></a>
            </div>
        </div>
    ))
}

export default {
    path: /^\/leveranciers$/i,
    Page: ({url}) => {
        let previousPage, pagination, nextPage, table, newButton, generateButton, searchInput;
        const self : HTMLDivElement = (
            <main>
                <section>
                    <div className="container">
                        <div className="title">
                            <h1>Leveranciers</h1>
                            <div className={"controls"}>
                                {generateButton = <button>Genereren</button>}
                                {newButton = <button>Nieuw</button>}
                            </div>
                        </div>
                        <div className="card">
                            <div className="controls">
                                {searchInput =
                                    <input type="text" placeholder="Zoeken..." style={{width: "300px"}}/>}
                                <div className="pagination">
                                    {previousPage = <a disabled={true}><Codicon name={"chevron-left"}/></a>}
                                    {pagination = <p>1 / ?</p>}
                                    {nextPage = <a disabled={true}><Codicon name={"chevron-right"}/></a>}
                                </div>
                            </div>
                            {table = <div className="table"></div>}
                        </div>
                    </div>
                </section>
            </main>
        );

        generateButton.onclick = () => {
            fetch("http://api.boodschappenservice.loc/leverancier?random&amount=100", {method: "POST"})
                .then(res => {
                    if(res.status === 200) {
                        window.update();
                        getLeveranciers();
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

            getLeveranciers(searchValue);
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
            getLeveranciers();
            window.update();
        }}/>);

        const getLeveranciers = (search = "") => {
            const url = new URL(window.location.href);
            const page = Math.max(parseInt(url.searchParams.get("page")) || 1, 1);

            const res = syncFetch(`http://api.boodschappenservice.loc/leveranciers?limit=100&offset=${(page - 1) * 100}&search=${search}`).json();

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
            table.append(...res.response.map((leverancier : Leverancier) => <Entry leverancier={leverancier} callback={() => getLeveranciers(search)}/>));
        }

        getLeveranciers(searchInput.value.trim());

        return self;
    }
} as Router;