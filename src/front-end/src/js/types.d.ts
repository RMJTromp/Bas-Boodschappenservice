export interface RequestData {
    url: URL
}

export interface Router {
    path: RegExp|RegExp[],
    Page: (data: RequestData) => HTMLElement
}

export interface Leverancier {
    id: number,
    naam: string,
    contact: string,
    email: string,
    adres: string,
    postcode: string,
    woonplaats: string,
}

export interface Artikel {
    id: number,
    omschrijving: string,
    inkoopPrijs: number,
    verkoopPrijs: number,
    voorraad: number,
    minVoorraad: number,
    maxVoorraad: number,
    foto: null|string,
    locatie: null|number,
    leverancier: Leverancier
}

export interface Klant {
    id: number,
    naam: string,
    email: string,
    adres: string,
    postcode: string,
    woonplaats: string,
}

export interface VerkoopOrder {
    id: number,
    klant: Klant,
    artikel: Artikel,
    datum: {
        date: string,
        timezone_type: number,
        timezone: string
    },
    aantal: 210,
    status: 1
}