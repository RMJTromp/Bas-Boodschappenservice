import { h } from "dom-chef";
import Codicon from "./codicon";
import Modal from "./modal";

export function Artikels() {
    const url = new URL(window.location.href);
    const page = Math.max(parseInt(url.searchParams.get("page")) || 1, 1);

    let loader, previousPage, pagination, nextPage, table, newButton, generateButton, searchInput;
    const container : HTMLDivElement = (
        <div className="container">
            <div className="title">
                <h1>Artikels {loader = <Codicon name={"loading"} style={{animation: "rotate 1s linear infinite"}}/>}</h1>
                <div style={{display: "flex", gap:"1rem"}}>
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
    );

    generateButton.onclick = () => {
        fetch("http://api.boodschappenservice.loc/artikel?random&amount=100", {method: "POST"})
            .then(() => getArtikels())
    }

    // check searchInput for changes after no action for 500ms
    let searchTimeout;
    const doneTyping = () => getArtikels(searchInput.value.trim());
    const inputEvent = () => {
        if(searchTimeout) clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => doneTyping(), 200);
    };
    searchInput.oninput = inputEvent;
    searchInput.onchange = inputEvent;
    searchInput.onkeyup = inputEvent;
    searchInput.value = url.searchParams.get("search") || "";

    newButton.onclick = () => {
        const modal = <Modal>
            <header>
                <h2>Nieuwe artikel</h2>
            </header>
            <main>
                <h4>artikel</h4>
                <label>Naam
                    <input type="text" id={"naam"}/>
                </label>
                <div style={{display: "grid", gridTemplateColumns: "65% 31%", gap: "1rem"}}>
                    <label>Adres
                        <input type="text" id={"adres"}/>
                    </label>
                    <label>Postcode
                        <input type="text" id={"postcode"} style={{textTransform: "uppercase"}}/>
                    </label>
                </div>
                <label>Plaats
                    <input type="text" id={"plaats"}/>
                </label>

                <h4 style={{marginTop: "15px"}}>Contactpersoon</h4>
                <label>Volledige Naam
                    <input type="text" id={"contact"}/>
                </label>
                <label>E-mail
                    <input type="email" id={"email"}/>
                </label>
            </main>
            <footer>
                <button onclick={() => modal.open = false}>Annuleren</button>
                <button class={"primary"} onclick={function() {
                    this.disabled = true;
                    fetch(`http://api.boodschappenservice.loc/artikel`, {
                        method: "POST",
                        body: JSON.stringify({
                            naam: modal.querySelector("input#naam").value,
                            adres: modal.querySelector("input#adres").value,
                            postcode: modal.querySelector("input#postcode").value,
                            woonplaats: modal.querySelector("input#plaats").value,
                            contact: modal.querySelector("input#contact").value,
                            email: modal.querySelector("input#email").value,
                        }),
                        headers: {
                            "Content-Type": "application/json"
                        }
                    }).then(async (res) => {
                        if(res.status === 200) {
                            modal.open = false;
                            getArtikels();
                        } else {
                            const response = await res.json();
                            modal.querySelector("main > div.alert")?.remove();
                            modal.querySelector("main").prepend(
                                <div className="alert">
                                    <Codicon name={"alert"}/>
                                    <p>{response.meta.exception ?? response.meta.status.message}</p>
                                </div>
                            )
                            this.disabled = false;
                        }
                    })
                }}>Opslaan</button>
            </footer>
        </Modal>;
        document.body.append(modal);
    }

    const getArtikels = (search = "") => {
        if(search) {
            let _url = new URL(window.location.href);
            if(_url.searchParams.get("search") !== search) {
                _url.searchParams.set("search", search);
                window.history.replaceState({}, "", _url.href);
            }
        }

        fetch(`http://api.boodschappenservice.loc/artikels?limit=100&offset=${(page - 1) * 100}&search=${search}`)
            .then(res => res.json())
            .then((res) => {
                loader.remove();
                const maxPage = Math.ceil((search ? res.meta.results : res.meta.total) / 100);
                previousPage.href = (() => {
                    let _url = new URL(window.location.href);
                    _url.searchParams.set("page", (page-1).toString());
                    return page !== 1 ? _url.href : "";
                })();
                if(page == 1) previousPage.setAttribute("disabled", "true");
                else previousPage.removeAttribute("disabled");
                nextPage.href = (() => {
                    let _url = new URL(window.location.href);
                    _url.searchParams.set("page", (page+1).toString());
                    return page !== maxPage ? _url.href : "";
                })();
                if(page === maxPage) nextPage.setAttribute("disabled", "true");
                else nextPage.removeAttribute("disabled");
                pagination.innerText = `${page} / ${maxPage}`;
                table.innerHTML = "";
                table.append(...res.response.map(artikel => {
                    const el = <div className="artikel">
                        <p>{artikel.naam}</p>
                        <p>{artikel.contact}<br/><a href={`mailto:${artikel.email}`}
                                                        style={{textTransform: "lowercase"}}>{artikel.email}</a></p>
                        <p>{artikel.adres}, {artikel.postcode}<br/>{artikel.woonplaats}</p>
                        <div className="controls">
                            <a onclick={() => {
                                let naamInput, adresInput, postcodeInput, plaatsInput, contactInput, emailInput;
                                const modal = <Modal>
                                    <header>
                                        <h2>Wijzig artikel</h2>
                                    </header>
                                    <main>
                                        <h4>artikel</h4>
                                        <label>Naam
                                            {naamInput = <input type="text" value={artikel.naam}/>}
                                        </label>
                                        <div style={{display: "grid", gridTemplateColumns: "65% 31%", gap: "1rem"}}>
                                            <label>Adres
                                                {adresInput = <input type="text" value={artikel.adres}/>}
                                            </label>
                                            <label>Postcode
                                                {postcodeInput = <input type="text" style={{textTransform: "uppercase"}} value={artikel.postcode}/>}
                                            </label>
                                        </div>
                                        <label>Plaats
                                            {plaatsInput = <input type="text" value={artikel.woonplaats}/>}
                                        </label>

                                        <h4 style={{marginTop: "15px"}}>Contactpersoon</h4>
                                        <label>Volledige Naam
                                            {contactInput = <input type="text" value={artikel.contact}/>}
                                        </label>
                                        <label>E-mail
                                            {emailInput = <input type="email" value={artikel.email}/>}
                                        </label>
                                    </main>
                                    <footer>
                                        <button onclick={() => modal.open = false}>Annuleren</button>
                                        <button class={"primary"} onclick={function() {
                                            this.disabled = true;
                                            fetch(`http://api.boodschappenservice.loc/artikel/${artikel.id}`, {
                                                method: "PATCH",
                                                body: JSON.stringify({
                                                    naam: naamInput.value,
                                                    adres: adresInput.value,
                                                    postcode: postcodeInput.value,
                                                    woonplaats: plaatsInput.value,
                                                    contact: contactInput.value,
                                                    email: emailInput.value,
                                                }),
                                                headers: {
                                                    "Content-Type": "application/json"
                                                }
                                            }).then(async (res) => {
                                                if(res.status === 200) {
                                                    modal.open = false;
                                                    getArtikels();
                                                } else {
                                                    const response = await res.json();
                                                    modal.querySelector("main > div.alert")?.remove();
                                                    modal.querySelector("main").prepend(
                                                        <div className="alert">
                                                            <Codicon name={"alert"}/>
                                                            <p>{response.meta.exception ?? response.meta.status.message}</p>
                                                        </div>
                                                    )
                                                    this.disabled = false;
                                                }
                                            })
                                        }}>Opslaan</button>
                                    </footer>
                                </Modal>;
                                document.body.append(modal);
                            }}><Codicon name={"pencil"}/></a>
                            <a onClick={() => {
                                fetch(`http://api.boodschappenservice.loc/artikel/${artikel.id}`, {method: "DELETE"})
                                    .then(res => {
                                        if(res.status === 200) el.remove();
                                    })
                            }}><Codicon name={"trash"}/></a>
                        </div>
                    </div>;
                    return el;
                }));
            })
    }

    getArtikels(searchInput.value.trim());

    return (
        <section id="artikels">
            {container}
        </section>
    )
}