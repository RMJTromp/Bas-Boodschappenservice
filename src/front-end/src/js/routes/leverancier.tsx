import {Router} from "../types";
import {Link} from "../components/sidebar";
import {h} from "dom-chef";
import syncFetch from "../utilities/sync-fetch";

export default {
    path: /^\/leverancier\/\d+$/i,
    Page: ({url}) => {
        const res = syncFetch(`http://api.boodschappenservice.loc/leverancier/${url.pathname.split("/")[2]}`);
        if(res.status === 200) {
            const leverancier = res.json().response;

            document.querySelector("body > aside > a[href='/leveranciers']")
                .after(<Link link={`/leverancier/${leverancier.id}`} text={`Leverancier #${leverancier.id}`} sub={true}/>);

            return (
                <main>
                    <section id="leveranciers">
                        <div className="container">
                            <div className="title">
                                <h1>Leverancier #{leverancier.id}</h1>
                                <div className={"controls"}>
                                    <button>Wijzig</button>
                                    <button>Verwijder</button>
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
                    <section id="artikels">
                        <div className="container">
                            <div className="title">
                                <h1>Artikels</h1>
                                <div className={"controls"}>
                                    <button>Wijzig</button>
                                    <button>Verwijder</button>
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
        }
    }
} as Router