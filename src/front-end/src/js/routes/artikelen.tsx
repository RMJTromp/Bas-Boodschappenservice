import {Artikel, Leverancier, Router} from "../types";
import {Modal as BaseModal} from "../components/modal";
import Codicon from "../components/codicon";
import {h} from "dom-chef";
import syncFetch from "../utilities/sync-fetch";

export function Modal(props : {callback: (artikel: Artikel) => void, artikel?: Artikel, [key : string] : any}) {
    const artikel = props.artikel ?? null;
    let omschrijnvingInput, minVoorraadInput, voorraadInput, maxVoorraadInput, inkoopPrijsInput, verkoopPrijsInput, leverancierInput;
    const self = <BaseModal>
        <header>
            <h2>{artikel ? "Wijzig Artikel" : "Nieuwe Artikel"}</h2>
        </header>
        <main>
            <label>Omschrijving
                {omschrijnvingInput = <input type="text" value={artikel?.omschrijving ?? ""}/>}
            </label>
            <div style={{display: "grid", gridTemplateColumns: "repeat(2, 48.5%)", gap: "1rem"}}>
                <label>Inkoop Prijs
                    {inkoopPrijsInput = <input type="number" value={artikel?.minVoorraad ?? 1.50}/>}
                </label>
                <label>Verkoop Prijs
                    {verkoopPrijsInput = <input type="number" value={artikel?.voorraad ?? 1.85}/>}
                </label>
            </div>
            <div style={{display: "grid", gridTemplateColumns: "repeat(3, 31.5%)", gap: "1rem"}}>
                <label>Minimum Voorraad
                    {minVoorraadInput = <input type="number" value={artikel?.minVoorraad ?? 1}/>}
                </label>
                <label>Voorraad
                    {voorraadInput = <input type="number" value={artikel?.voorraad ?? 50}/>}
                </label>
                <label>Maximum Voorraad
                    {maxVoorraadInput = <input type="number" value={artikel?.maxVoorraad ?? 100}/>}
                </label>
            </div>
            <label>Leverancier ID
                {leverancierInput = <input type="number" value={artikel?.leverancier.id ?? undefined}/>}
            </label>
        </main>
        <footer>
            <button onClick={() => self.open = false}>Annuleren</button>
            <button className={"primary"} onClick={function () {
                this.disabled = true;

                const body = {
                    omschrijving: omschrijnvingInput.value,
                    minVoorraad: minVoorraadInput.value,
                    voorraad: voorraadInput.value,
                    maxVoorraad: maxVoorraadInput.value,
                    inkoopPrijs: inkoopPrijsInput.value,
                    verkoopPrijs: verkoopPrijsInput.value,
                    leverancier: leverancierInput.value
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

export function Entry(props : {artikel : Artikel, callback: (artikel: Artikel) => void, [key : string] : any}) {
    const artikel = props.artikel;

    let foto = artikel.foto;
    if(!foto) {
        foto = "https://www.grouphealth.ca/wp-content/uploads/2018/05/placeholder-image.png";
    } else if(foto.startsWith("AHI")) {
        let [id, rev] = foto.split(":");
        foto = `https://static.ah.nl/dam/product/${id}?revLabel=${rev}&rendition=200x200_JPG_Q85&fileType=binary`;
    }

    let negativeQuantity = artikel.voorraad <= artikel.minVoorraad,
        lowQuantity = ((artikel.voorraad - artikel.minVoorraad) / (artikel.maxVoorraad - artikel.minVoorraad)) < .05;

    const self = (
        <div className={`artikel ${lowQuantity ? (negativeQuantity ? "danger" : "warning") : ""}`}>
            <div className="details">
                <div>
                    <img src={foto} alt={artikel.omschrijving} width={64} height={64} style={{mixBlendMode: "multiply", objectPosition:"center", objectFit: "contain"}}/>
                    <div className="details">
                        <b>{artikel.omschrijving}</b>
                        <p>Inkoop Prijs: <b>€{artikel.inkoopPrijs.toFixed(2)}</b> | Verkoop Prijs: <b>€{artikel.verkoopPrijs.toFixed(2)}</b></p>
                        <p class={"voorraad"}><span>{negativeQuantity || lowQuantity ? <Codicon name={"warning"}/> : undefined} Voorraad: <b>{artikel.voorraad}</b></span> | Min Voorraad: <b>{artikel.minVoorraad}</b> | Max Voorraad: <b>{artikel.maxVoorraad}</b></p>
                        {typeof artikel.leverancier === "object" ? <span>Leverancier: <a href={`/leverancier/${artikel.leverancier.id}`}>{artikel.leverancier.naam}</a></span> : undefined}
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