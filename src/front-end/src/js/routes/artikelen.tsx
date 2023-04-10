import {Artikel, Router} from "../types";
import {Modal as BaseModal} from "../components/modal";
import Codicon from "../components/codicon";
import {h} from "dom-chef";
import syncFetch from "../utilities/sync-fetch";

export function Modal(props : {callback: (artikel: Artikel) => void, artikel?: Artikel, [key : string] : any}) {
    const artikel = props.artikel ?? null;
    let naamInput, adresInput, postcodeInput, plaatsInput, contactInput, emailInput;
    const self = <BaseModal>
        <header>
            <h2>{artikel ? "Wijzig Artikel" : "Nieuwe Artikel"}</h2>
        </header>
        <main>
            <h4>artikel</h4>
            <label>Naam
                {naamInput = <input type="text" value={artikel?.naam ?? ""}/>}
            </label>
            <div style={{display: "grid", gridTemplateColumns: "65% 31%", gap: "1rem"}}>
                <label>Adres
                    {adresInput = <input type="text" value={artikel?.adres ?? ""}/>}
                </label>
                <label>Postcode
                    {postcodeInput = <input type="text" style={{textTransform: "uppercase"}} value={artikel?.postcode ?? ""}/>}
                </label>
            </div>
            <label>Plaats
                {plaatsInput = <input type="text" value={artikel?.woonplaats ?? ""}/>}
            </label>

            <h4 style={{marginTop: "15px"}}>Contactpersoon</h4>
            <label>Volledige Naam
                {contactInput = <input type="text" value={artikel?.contact ?? ""}/>}
            </label>
            <label>E-mail
                {emailInput = <input type="email" value={artikel?.email ?? ""}/>}
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

                const promise = artikel
                    ? fetch(`http://api.boodschappenservice.loc/artikel/${artikel.id}`, {
                        method: "PATCH",
                        body: JSON.stringify(body),
                        headers: {
                            "Content-Type": "application/json"
                        }
                    })
                    : fetch(`http://api.boodschappenservice.loc/artikel`, {
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

function Entry(props : {artikel : Artikel, callback: (artikel: Artikel) => void, [key : string] : any}) {
    const artikel = props.artikel;

    let foto = artikel.foto;
    if(foto && foto.startsWith("AHI")) {
        let [id, rev] = foto.split(":");
        foto = `https://static.ah.nl/dam/product/${id}?revLabel=${rev}&rendition=200x200_JPG_Q85&fileType=binary`;
    }

    let negativeQuantity = artikel.voorraad <= artikel.minVoorraad,
        lowQuantity = ((artikel.voorraad - artikel.minVoorraad) / (artikel.maxVoorraad - artikel.minVoorraad)) < .05;

    const self = (
        <div className={`artikel ${lowQuantity ? (negativeQuantity ? "danger" : "warning") : ""}`}>
            <div className="details">
                <div>
                    <img src={foto} alt={artikel.omschrijving} width={64} height={64} style={{mixBlendMode: "multiply"}}/>
                    <div className="details">
                        <b>{artikel.omschrijving}</b>
                        <p>Inkoop Prijs: <b>€{artikel.inkoopPrijs.toFixed(2)}</b> | Verkoop Prijs: <b>€{artikel.verkoopPrijs.toFixed(2)}</b></p>
                        <p class={"voorraad"}><span>{negativeQuantity || lowQuantity ? <Codicon name={"warning"}/> : undefined} Voorraad: <b>{artikel.voorraad}</b></span> | Min Voorraad: <b>{artikel.minVoorraad}</b> | Max Voorraad: <b>{artikel.maxVoorraad}</b></p>
                        <span>Leverancier: <a href={`/leverancier/${artikel.leverancier.id}`}>{artikel.leverancier.naam}</a></span>
                    </div>
                </div>
            </div>
            <div className="controls">
                <a onClick={() => {
                    document.body.append(<Modal artikel={artikel} callback={props.callback}/>);
                }}><Codicon name={"pencil"}/></a>
                <a onClick={() => {
                    fetch(`http://api.boodschappenservice.loc/artikel/${artikel.id}`, {method: "DELETE"})
                        .then(res => {
                            if(res.status === 200) {
                                self.remove();
                                window.update();
                            }
                        })
                }}><Codicon name={"trash"}/></a>
                <a href={`/artikel/${artikel.id}`}><Codicon name={"chevron-right"}/></a>
            </div>
        </div>
    )
    return self;
}

export default {
    path: /^\/artikelen$/i,
    Page: ({url}) => {
        let previousPage, pagination, nextPage, table, newButton, generateButton, searchInput;
        const self : HTMLDivElement = (
            <main>
                <section>
                    <div className="container">
                        <div className="title">
                            <h1>Artikelen</h1>
                            <div className={"controls"}>
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
            fetch("http://api.boodschappenservice.loc/artikel?random&amount=100", {method: "POST"})
                .then(res => {
                    if(res.status === 200) {
                        window.update();
                        getArtikelen();
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

            getArtikelen(searchValue);
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
            getArtikelen();
            window.update();
        }}/>);

        const getArtikelen = (search = "") => {
            const url = new URL(window.location.href);
            const page = Math.max(parseInt(url.searchParams.get("page")) || 1, 1);

            const res = syncFetch(`http://api.boodschappenservice.loc/artikelen?limit=100&offset=${(page - 1) * 100}&search=${search}`).json();

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
            table.append(...res.response.map((artikel : Artikel) => <Entry artikel={artikel} callback={() => getArtikelen(search)}/>));
        }

        getArtikelen(searchInput.value.trim());

        return self;
    }
} as Router;