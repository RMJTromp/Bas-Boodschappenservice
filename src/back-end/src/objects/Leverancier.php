<?php

    namespace Boodschappenservice\objects;

    use Boodschappenservice\attributes\Column;
    use Boodschappenservice\attributes\Table;

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
            maxLength: 30
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

    }