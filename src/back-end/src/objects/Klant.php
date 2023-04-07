<?php

namespace Boodschappenservice\objects;

use Boodschappenservice\attributes\Column;
use Boodschappenservice\attributes\Table;
use Boodschappenservice\utilities\MockData;

#[Table("KLANTEN")]
class Klant extends BaseObject {

    #[Column("klantId",
        primary: true,
        immutable: true
    )]
    private int $id;

    #[Column("klantNaam",
        regexp: '/^[\\w@&+-]+(?: [\\w@&+-]+)*$/iu',
        minLength: 3,
        maxLength: 15
    )]
    private string $naam;

    #[Column("klantEmail",
        regexp: '/^[\\w\\-\\.]+@([\\w-]+\\.)+[\\w-]{2,4}$/u',
        minLength: 3,
        maxLength: 320
    )]
    private string $email;

    #[Column("klantAdres",
        regexp: '/^[^\\s]+(?: [^\\s]+)*$/iu',
        minLength: 3,
        maxLength: 30
    )]
    private string $adres;

    #[Column("klantPostcode",
        regexp: '/^\\d{4}[a-z]{2}$/iu',
        minLength: 6,
        maxLength: 6
    )]
    private string $postcode;

    #[Column("klantWoonplaats",
        regexp: '/^[^\\s]+(?: [^\\s]+)*$/iu',
        minLength: 3,
        maxLength: 25
    )]
    private string $woonplaats;

    public static function generateRandom() : Klant {
        $mockData = MockData::getInstance();

        $naam = $mockData->names->random() . " " . $mockData->surnames->random();
        $email = strtolower(MockData::strip($naam) . "@gmail.com");

        $klant = Klant::create();
        $klant->naam = $naam;
        $klant->email = $email;
        $klant->adres = $mockData->street->random() . " " . rand(1, 350);
        $klant->postcode = rand(1000, 9999) . chr(rand(65, 90)) . chr(rand(65, 90));
        $klant->woonplaats = $mockData->plaats->random();
        $klant->save();
        return $klant;
    }

}