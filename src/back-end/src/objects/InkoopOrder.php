<?php

    namespace Boodschappenservice\objects;

    use Boodschappenservice\attributes\Column;
    use Boodschappenservice\attributes\ProxyProperty;
    use Boodschappenservice\attributes\Table;
    use Boodschappenservice\utilities\MockData;

    /**
     * @property-read int $id
     * @property Leverancier $leverancier
     * @property \DateTime $datum
     * @property Artikel $artikel
     * @property int $aantal
     * @property bool $geleverd
     */
    #[Table("inkooporders")]
    class InkoopOrder extends BaseObject {

        #[Column("inkOrdId",
            primary: true,
            immutable: true
        )]
        private int $id;

        #[Column("levId")]
        private int $_levId;

        #[ProxyProperty("_levId")]
        private Leverancier $leverancier;

        #[Column("inkOrdDatum")]
        private int $_datum;

        #[ProxyProperty("_datum")]
        private \DateTime $datum;

        #[Column("artId")]
        private int $_artId;

        #[ProxyProperty("_artId")]
        private Artikel $artikel;

        #[Column("inkOrdBestAantal", min: 1)]
        private int $aantal;

        #[Column("inkOrdStatus")]
        private bool $geleverd;

        protected function __construct(int $id = -1) {
            parent::__construct($id);
        }

        public function __get(string $name) {
            if ($name === "leverancier") {
                if (empty($this->leverancier))
                    $this->leverancier = Leverancier::get($this->_levId);
                return $this->leverancier;
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
            if ($name === "leverancier") {
                if ($value instanceof Leverancier) {
                    $this->_levId = $value->id;
                    $this->leverancier = $value;
                } else throw new \InvalidArgumentException("Value must be an instance of Leverancier");
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

        public static function generateRandom() : InkoopOrder {
            $inkoopOrder = InkoopOrder::create();
            $inkoopOrder->artikel = Artikel::random() ?? Artikel::generateRandom();
            $inkoopOrder->_artId = $inkoopOrder->artikel->id;
            $inkoopOrder->leverancier = Leverancier::random() ?? Leverancier::generateRandom();
            $inkoopOrder->_levId = $inkoopOrder->leverancier->id;
            $inkoopOrder->aantal = rand(1, 300);
            $inkoopOrder->_datum = time();
            $inkoopOrder->geleverd = rand(0, 1) === 1;
            $inkoopOrder->save();
            return $inkoopOrder;
        }

    }