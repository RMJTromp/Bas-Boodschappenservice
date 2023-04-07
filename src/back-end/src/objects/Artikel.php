<?php

    namespace Boodschappenservice\objects;

    use Boodschappenservice\attributes\Column;
    use Boodschappenservice\attributes\Table;

    /**
     * @property-read int $id
     * @property string $omschrijving
     * @property ?float $inkoopPrijs
     * @property ?float $verkoopPrijs
     * @property int $voorraad
     * @property int $minVoorraad
     * @property int $maxVoorraad
     * @property Leverancier $leverancier
     */
    #[Table("ARTIKELEN")]
    class Artikel extends BaseObject {

        #[Column("artId",
            primary: true,
            immutable: true
        )]
        private int $id;

        #[Column("artOmschrijving",
            regexp: '/^[^\\s]+(?: [^\\s]+)*$/iu',
            minLength: 1,
            maxLength: 60
        )]
        private string $omschrijving;

        #[Column("artInkoop", nullable: true, min: 0)]
        private ?float $inkoopPrijs;

        #[Column("artVerkoop", nullable: true, min: 0)]
        private ?float $verkoopPrijs;

        #[Column("artVoorraad", min: 0)]
        private int $voorraad;

        #[Column("artMinVoorraad", min: 0)]
        private int $minVoorraad;

        #[Column("artMaxVoorraad", min: 0)]
        private int $maxVoorraad;

        #[Column("artLocatie", nullable: true, min: 0)]
        private int $locatie;

        #[Column("levId")]
        private int $_levId;

        private ?Leverancier $leverancier;

        protected function __construct(int $id = -1) {
            parent::__construct($id);
        }

    }