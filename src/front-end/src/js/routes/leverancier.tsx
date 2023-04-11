import {Artikel, Leverancier, Router} from "../types";
import {Link} from "../components/sidebar";
import {h} from "dom-chef";
import syncFetch from "../utilities/sync-fetch";
import {Entry as ArtikelEntry} from "./artikelen";

export default {
    path: /^\/leverancier\/\d+$/i,
    Page: ({url}) => {
        const res = syncFetch(`http://api.boodschappenservice.loc/leverancier/${url.pathname.split("/")[2]}`);
        if(res.status === 200) {
            const leverancier = res.json().response as Leverancier;

            document.querySelector("body > aside > a[href='/leveranciers']")
                .after(<Link link={`/leverancier/${leverancier.id}`} text={`Leverancier #${leverancier.id}`} sub={true}/>);

            const self = (
                <main>
                    <section id="leveranciers">
                        <div className="container">
                            <div className="title">
                                <h1>Leverancier #{leverancier.id}</h1>
                                <div className={"controls"}>
                                    <button onclick={() => {
                                        fetch(`http://api.boodschappenservice.loc/leverancier/${leverancier.id}`, {
                                            method: "DELETE"
                                        }).then(res => {
                                            if(res.status === 200) {
                                                window.history.pushState({}, "", "/leveranciers");
                                            }
                                        })
                                    }}>Verwijder</button>
                                </div>
                            </div>
                            <div className="card"
                                 style={{display: "grid", gridAutoFlow: "column", gridTemplateColumns: "1fr 1fr", gap: "1rem"}}>
                                <div>
                                    <h4>Leverancier</h4>
                                    <label>Naam
                                        <input type="text" value={leverancier?.naam ?? ""} readOnly={true}/>
                                    </label>
                                    <div style={{display: "grid", gridTemplateColumns: "65% 32%", gap: "1rem"}}>
                                        <label>Adres
                                            <input type="text" value={leverancier?.adres ?? ""} readOnly={true}/>
                                        </label>
                                        <label>Postcode
                                            <input type="text" style={{textTransform: "uppercase"}}
                                                   value={leverancier?.postcode ?? ""} readOnly={true}/>
                                        </label>
                                    </div>
                                    <label>Plaats
                                        <input type="text" value={leverancier?.woonplaats ?? ""} readOnly={true}/>
                                    </label>
                                </div>
                                <div>
                                    <h4>Contactpersoon</h4>
                                    <label>Volledige Naam
                                        <input type="text" value={leverancier?.contact ?? ""} readOnly={true}/>
                                    </label>
                                    <label>E-mail
                                        <input type="email" value={leverancier?.email ?? ""} readOnly={true}/>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </section>

                </main>
            )

            fetch(`http://api.boodschappenservice.loc/leverancier/${leverancier.id}/artikelen`)
                .then(res => res.json())
                .then(res => {
                    if(res.response.length > 0) {
                        self.append(<section id="artikels">
                            <div className="container">
                                <div className="title">
                                    <h1>Artikelen</h1>
                                </div>
                                <div className="card">
                                    <div className="table">
                                        {res.response.map((artikel : Artikel) => {
                                            return <ArtikelEntry artikel={artikel} callback={() => {}} />;
                                        })}
                                    </div>
                                </div>
                            </div>
                        </section>)
                    }
                })

            return self;
        }
    }
} as Router