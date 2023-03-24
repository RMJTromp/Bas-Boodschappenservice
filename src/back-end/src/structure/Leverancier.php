<?php

    namespace Boodschappenservice\structure;

    use Boodschappenservice\utilities\ArrayList;

    /**
     * @property-read int $id
     */
    class Leverancier implements \JsonSerializable {

        /**
         * @return Array<Leverancier>
         * @throws \Exception
         */
        public static function getAll() : array {
            global $conn;
            $stmt = $conn->prepare("SELECT id FROM `leveranciers`");
            $res = $stmt->execute();
            if($res) {
                $leveranciers = new ArrayList();
                $stmt->bind_result($id);
                while($stmt->fetch()) {
                    $leveranciers->add($id);
                }

                return $leveranciers->map(function($id) {
                    return Leverancier::get($id);
                })->getArray();
            } else throw new \Exception($stmt->error, 500);
        }

        /**
         * @throws \Exception
         */
        public static function create(string $naam, string $contact, string $email, string $adres, string $postcode, string $woonplaats) : Leverancier {
            global $conn;
            $stmt = $conn->prepare("INSERT INTO `leveranciers` (naam, contact, email, adres, postcode, woonplaats) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $naam, $contact, $email, $adres, $postcode, $woonplaats);
            $res = $stmt->execute();
            if($res) {
                $id = $conn->insert_id;
                return Leverancier::get($id);
            } else throw new \Exception($stmt->error, 500);
        }

        /**
         * @throws \Exception
         */
        public static function get(int $id) : Leverancier {
            return new Leverancier($id);
        }

        public int $id;

        public string $naam, $contact, $email, $adres, $postcode, $woonplaats;

        /**
         * @throws \Exception
         */
        private function __construct(int $id) {
            global $conn;
            $stmt = $conn->prepare("SELECT * FROM `leveranciers` WHERE id = ?");
            $stmt->bind_param("i", $id);
            $res = $stmt->execute();
            if($res) {
                $stmt->bind_result($id, $naam, $contact, $email, $adres, $postcode, $woonplaats);
                if($stmt->fetch() === null)
                    throw new \Exception("Leverancier met id $id bestaat niet", 404);

                $this->id = $id;
                $this->naam = $naam;
                $this->contact = $contact;
                $this->email = $email;
                $this->adres = $adres;
                $this->postcode = $postcode;
                $this->woonplaats = $woonplaats;
            } else throw new \Exception($stmt->error, 500);
        }

        public function save() {
            global $conn;
            $stmt = $conn->prepare("UPDATE `leveranciers` SET naam = ?, contact = ?, email = ?, adres = ?, postcode = ?, woonplaats = ? WHERE id = ?");
            $stmt->bind_param("ssssssi", $this->naam, $this->contact, $this->email, $this->adres, $this->postcode, $this->woonplaats, $this->id);
            if(!$stmt->execute()) throw new \Exception($stmt->error, 500);
        }

        public function delete() {
            global $conn;
            $stmt = $conn->prepare("DELETE FROM `leveranciers` WHERE id = ?");
            $stmt->bind_param("i", $this->id);
            if(!$stmt->execute()) throw new \Exception($stmt->error, 500);
        }

        public function jsonSerialize(): array {
            return [
                "id" => $this->id,
                "naam" => $this->naam,
                "contact" => $this->contact,
                "email" => $this->email,
                "adres" => $this->adres,
                "postcode" => $this->postcode,
                "woonplaats" => $this->woonplaats
            ];
        }
    }