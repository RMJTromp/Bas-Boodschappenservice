import { h } from "dom-chef";
import syncFetch from "../utilities/sync-fetch";
import {VerkoopOrder} from "../types";

function resolveStatus (status :number) {
    if(status === 1) return "In behandeling";
    else if(status === 2) return "Wordt verzamelt";
    else if(status === 3) return "Onderweg";
    else if(status === 4) return "Afgeleverd";
}
export default {
    path: /^\/verkooporders$/,
    Page: () => {
        const verkoopOrders = syncFetch(`http://api.boodschappenservice.loc/verkooporders`).json().response as VerkoopOrder[];

        return <main>
            <section>
                <div className="container">
                    <h1>Verkooporders</h1>
                    <div className="card">
                        <div className="table">
                            {verkoopOrders.map((order) => {
                                return <div className="verkooporder">
                                    <p>{order.klant.naam}<br/><a href={`mailto:${order.klant.email}`}>{order.klant.email}</a><br/>{order.klant.adres}, {order.klant.postcode}, {order.klant.woonplaats}</p>
                                    <div className="artikel" style={{textAlign: "right"}}>
                                        <p><b>{order.aantal}x</b> <a href={`/leverancier/${order.artikel.leverancier.id}`}>{order.artikel.omschrijving}</a></p>
                                        <p>Prijs: <b>€{(order.artikel.verkoopPrijs * order.aantal).toFixed(2)}</b></p>
                                        <p>status: <b>{resolveStatus(order.status)}</b></p>
                                    </div>
                                </div>
                            })}
                        </div>
                    </div>
                </div>
            </section>
        </main>
    }
}