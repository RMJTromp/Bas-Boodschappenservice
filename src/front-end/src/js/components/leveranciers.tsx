import { h } from "dom-chef";
import {Codicon} from "./codicon";
import Modal from "./modal";

export function Leveranciers() {
    const url = new URL(window.location.href);
    const page = Math.max(parseInt(url.searchParams.get("page")) || 1, 1);

    let loader, previousPage, pagination, nextPage, table, newButton, generateButton, searchInput;
    const container : HTMLDivElement = (
        <div className="container">
            <div className="title">
                <h1>Leveranciers {loader = <Codicon name={"loading"} style={{animation: "rotate 1s linear infinite"}}/>}</h1>
                <div style={{display: "flex", gap:"1rem"}}>
                    {generateButton = <button>Genereren</button>}
                    {newButton = <button>Nieuw</button>}
                </div>
            </div>
            <div className="card">
                <div className="controls">
                    {searchInput = <input type="text" placeholder="Zoeken..."/>}
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

    // check searchInput for changes after no action for 500ms
    let searchTimeout;
    const doneTyping = () => {}
    const inputEvent = () => {
        if(searchTimeout) clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => doneTyping(), 500);
    };
    searchInput.oninput = inputEvent;
    //

    newButton.onclick = () => {
        document.body.append(<Modal>
            <header>
                <h2>Nieuwe Leverancier</h2>
            </header>
            <main>
                <h4>Leverancier</h4>
                <label>Naam
                    <input type="text" placeholder={"Naam..."}/>
                </label>
                <label>Adres
                    <input type="text" placeholder={"Adres..."}/>
                </label>
                <label>Postcode
                    <input type="text" placeholder={"Postcode..."}/>
                </label>
                <label>Plaats
                    <input type="text" placeholder={"Naam..."}/>
                </label>

                <h4>Contactpersoon</h4>
                <label>Volledige Naam
                    <input type="text" placeholder={"Naam..."}/>
                </label>
                <label>Naam
                    <input type="text" placeholder={"Naam..."}/>
                </label>
            </main>
            <footer>
                <button>Annuleren</button>
                <button>Opslaan</button>
            </footer>
        </Modal>);
    }

    fetch(`http://api.boodschappenservice.loc/leveranciers?limit=100&offset=${(page - 1) * 100}`)
        .then(res => res.json())
        .then((res) => {
            loader.remove();
            const maxPage = Math.ceil(res.meta.total / 100);
            previousPage.href = page !== 1 ? `/leveranciers?page=${page-1}` : "";
            if(page == 1) previousPage.setAttribute("disabled", "true");
            else previousPage.removeAttribute("disabled");
            nextPage.href = page !== maxPage ? `/leveranciers?page=${page+1}` : "";
            if(page === maxPage) nextPage.setAttribute("disabled", "true");
            else nextPage.removeAttribute("disabled");
            pagination.innerText = `${page} / ${maxPage}`;
            table.append(...res.response.map(leverancier => {
                return (
                    <a href={`/leverancier/${leverancier.id}`} className="leverancier">
                        <p>{leverancier.naam}</p>
                        <p>{leverancier.contact}</p>
                        <p>{leverancier.email}</p>
                    </a>
                )
            }));
        })

    return (
        <section id="leveranciers">
            {container}
        </section>
    )
}