<?php

    namespace Boodschappenservice\objects;

    use Boodschappenservice\attributes\Column;
    use Boodschappenservice\attributes\ProxyProperty;
    use Boodschappenservice\attributes\Table;
    use Boodschappenservice\utilities\MockData;

    /**
     * @property-read int $id
     * @property string $omschrijving
     * @property ?float $inkoopPrijs
     * @property ?float $verkoopPrijs
     * @property int $voorraad
     * @property int $minVoorraad
     * @property int $maxVoorraad
     * @property string $foto
     * @property ?int $locatie
     * @property Leverancier $leverancier
     */
    #[Table("artikelen")]
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

        #[Column("artFoto",
            nullable: true
        )]
        private ?string $foto;

        #[Column("artLocatie", nullable: true, min: 0)]
        private ?int $locatie;

        #[Column("levId")]
        private int $_levId;

        #[ProxyProperty("_levId")]
        private ?Leverancier $leverancier;

        protected function __construct(int $id = -1) {
            parent::__construct($id);
        }

        public function __get(string $name) {
            if ($name === "leverancier") {
                if (empty($this->leverancier))
                    if(empty($this->_levId)) return null;
                    $this->leverancier = Leverancier::get($this->_levId);
                return $this->leverancier;
            }
            return parent::__get($name);
        }

        public function __set(string $name, mixed $value): void {
            if ($name === "leverancier") {
                if ($value instanceof Leverancier) {
                    $this->_levId = $value->id;
                    $this->leverancier = $value;
                } else throw new \InvalidArgumentException("Value must be an instance of Leverancier");
            } else parent::__set($name, $value);
        }

        public static function byLeverancier(Leverancier $leverancier) : array {
            global $conn;
            $table = self::getTable()->name;
            $primaryKey = self::getPrimaryProperty()[1]->name;
            $stmt = $conn->prepare("SELECT $primaryKey FROM $table WHERE levId = ?");
            $levId = $leverancier->id;
            $stmt->bind_param("i", $levId);
            $stmt->execute();
            $res = $stmt->get_result();
            if($res->num_rows === 0) return [];
            $ids = [];
            while($row = $res->fetch_assoc()) {
                $ids[] = $row[$primaryKey];
            }
            return array_map(fn($id) => self::get($id), $ids);
        }

        public static function generateRandom() : Artikel {
            $mockData = MockData::getInstance();

            $product = $mockData->products->random();

            $artikel = Artikel::create();
            $artikel->omschrijving = $product['omschrijving'];
            $artikel->verkoopPrijs = $product['verkoopPrijs'];
            $artikel->inkoopPrijs = floor($artikel->verkoopPrijs * 0.8 * 100) / 100;
            $artikel->minVoorraad = rand(50, 100);
            $artikel->maxVoorraad = rand(250, 500);
            $artikel->voorraad = rand(0, $artikel->maxVoorraad);
            $artikel->foto = $product['foto'];
            $artikel->leverancier = Leverancier::random() ?? Leverancier::generateRandom();
            $artikel->_levId = $artikel->leverancier->id; // yes, smh
            $artikel->save();

            return $artikel;
        }

    }