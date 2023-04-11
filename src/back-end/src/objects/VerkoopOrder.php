<?php

    namespace Boodschappenservice\objects;

    use Boodschappenservice\attributes\Column;
    use Boodschappenservice\attributes\ProxyProperty;
    use Boodschappenservice\attributes\Table;
    use Boodschappenservice\utilities\MockData;

    /**
     * @property-read int $id
     * @property Klant $klant
     * @property \DateTime $datum
     * @property Artikel $artikel
     * @property int $aantal
     * @property int $status
     */
    #[Table("verkooporders")]
    class VerkoopOrder extends BaseObject {

        #[Column("verkOrdId",
            primary: true,
            immutable: true
        )]
        private int $id;

        #[Column("klantId")]
        private int $_klantId;

        #[ProxyProperty("_klantId")]
        private Klant $klant;

        #[Column("artId")]
        private int $_artId;

        #[ProxyProperty("_artId")]
        private Artikel $artikel;

        #[Column("verkOrdDatum")]
        private int $_datum;

        #[ProxyProperty("_datum")]
        private \DateTime $datum;

        #[Column("verkOrdBestAantal", min: 1)]
        private int $aantal;

        /**
         * 1 = genoteerd in deze tabel
         * 2 = magazijnmedewerker verzamelt het artikel (picking)
         * 3 = tas met artikel is bij de bezorger
         * 4 = tas met artikel is afgeleverd bij de klant
         */
        #[Column("verkOrdStatus")]
        private int $status;

        protected function __construct(int $id = -1) {
            parent::__construct($id);
        }

        public function __get(string $name) {
            if ($name === "klant") {
                if (empty($this->klant))
                    $this->klant = Klant::get($this->_klantId);
                return $this->klant;
            } else if($name === "artikel") {
                if (empty($this->artikel))
                    $this->artikel = Artikel::get($this->_artId);
                return $this->artikel;
            } else if($name === "datum") {
                return \DateTime::createFromFormat("U", $this->_datum);
            }
            return parent::__get($name);
        }

        public function __set(string $name, mixed $value): void {
            if ($name === "klant") {
                if ($value instanceof Klant) {
                    $this->_klantId = $value->id;
                    $this->klant = $value;
                } else throw new \InvalidArgumentException("Value must be an instance of Klant");
            } else if ($name === "artikel") {
                if ($value instanceof Artikel) {
                    $this->_artId = $value->id;
                    $this->artikel = $value;
                } else throw new \InvalidArgumentException("Value must be an instance of Artikel");
            } else if ($name === "datum") {
                if ($value instanceof \DateTime) {
                    $this->_datum = $value->getTimestamp();
                } else throw new \InvalidArgumentException("Value must be an instance of DateTime");
            } else parent::__set($name, $value);
        }

        public static function generateRandom() : VerkoopOrder {
            $inkoopOrder = VerkoopOrder::create();
            $inkoopOrder->klant = Klant::random() ?? Klant::generateRandom();
            $inkoopOrder->_klantId = $inkoopOrder->klant->id;
            $inkoopOrder->artikel = Artikel::random() ?? Artikel::generateRandom();
            $inkoopOrder->_artId = $inkoopOrder->artikel->id;
            $inkoopOrder->aantal = rand(1, 300);
            $inkoopOrder->_datum = time();
            $inkoopOrder->status = rand(1, 4);
            $inkoopOrder->save();
            return $inkoopOrder;
        }

    }