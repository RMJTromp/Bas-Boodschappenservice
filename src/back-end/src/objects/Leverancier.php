<?php

    namespace Boodschappenservice\objects;

    use Boodschappenservice\attributes\Column;
    use Boodschappenservice\attributes\Table;
    use Boodschappenservice\utilities\MockData;

    /**
     * @property-read int $id Leverancier ID
     * @property string $naam Naam leverancier
     * @property string $contact Contactpersoon
     * @property string $email E-mailadres contactpersoon
     * @property string $adres Adres leverancier
     * @property string $postcode Postcode leverancier
     * @property string $woonplaats Woonplaats leverancier
     */
    #[Table("LEVERANCIERS")]
    class Leverancier extends BaseObject{

        #[Column("levId",
            primary: true,
            immutable: true
        )]
        private int $id;

        #[Column("levNaam",
            regexp: '/^[\\w@&+-]+(?: [\\w@&+-]+)*$/iu',
            minLength: 3,
            maxLength: 15
        )]
        private string $naam;

        #[Column("levContact",
            regexp: '/^[\w-]+(?: [\\w-]+)*$/iu',
            minLength: 2,
            maxLength: 20
        )]
        private string $contact;

        #[Column("levEmail",
            regexp: '/^[\\w\\-\\.]+@([\\w-]+\\.)+[\\w-]{2,4}$/u',
            minLength: 3,
            maxLength: 320
        )]
        private string $email;

        #[Column("levAdres",
            regexp: '/^[^\\s]+(?: [^\\s]+)*$/iu',
            minLength: 3,
            maxLength: 30
        )]
        private string $adres;

        #[Column("levPostcode",
            regexp: '/^\\d{4}[a-z]{2}$/iu',
            minLength: 6,
            maxLength: 6
        )]
        private string $postcode;

        #[Column("levWoonplaats",
            regexp: '/^[^\\s]+(?: [^\\s]+)*$/iu',
            minLength: 3,
            maxLength: 25
        )]
        private string $woonplaats;

        public static function generateRandom() : Leverancier {
            $mockData = MockData::getInstance();

            $company = $mockData->companies->random();
            $contact = $mockData->names->random() . " " . $mockData->surnames->random();
            $email = strtolower(MockData::strip($contact) . "@" . MockData::strip($company) . ".nl");

            $leverancier = Leverancier::create();
            $leverancier->naam = $company;
            $leverancier->contact = $contact;
            $leverancier->email = $email;
            $leverancier->adres = $mockData->street->random() . " " . rand(1, 350);
            $leverancier->postcode = rand(1000, 9999) . chr(rand(65, 90)) . chr(rand(65, 90));
            $leverancier->woonplaats = $mockData->plaats->random();
            $leverancier->save();
            return $leverancier;
        }

    }