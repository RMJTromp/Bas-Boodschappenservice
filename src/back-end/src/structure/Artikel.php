<?php

    namespace Boodschappenservice\structure;

    /**
     * @property-read int $id
     */
    class Artikel {

        public int $id;

        private string $omschrijving;
        private float $inkoopPrijs, $verkoopPrijs;
        private int $voorraad, $minVoorraad, $maxVoorraad, $locatie, $supplier;

        public function __construct() {
        }

        public function save() : void {
//            $conn
        }


    }