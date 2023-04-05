<?php

namespace Boodschappenservice\objects;

use Boodschappenservice\utilities\ArrayList;

class Klant implements \JsonSerializable
{

    /**
     * @return Array<Klant>
     * @throws \Exception
     */
    public static function getAll(): array
    {
        global $conn;
        $stmt = $conn->prepare("SELECT id FROM `klanten`");
        $res = $stmt->execute();
        if ($res) {
            $klanten = new ArrayList();
            $stmt->bind_result($id);
            while ($stmt->fetch()) {
                $klanten->add($id);
            }

            return $klanten->map(function ($id) {
                return Klant::get($id);
            })->getArray();
        } else throw new \Exception($stmt->error, 500);
    }

    /**
     * @throws \Exception
     */
    public static function create(string $naam, string $adres, string $postcode, string $woonplaats, string $telefoon): Klant
    {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO `klanten` (naam, adres, postcode, woonplaats, telefoon) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $naam, $adres, $postcode, $woonplaats, $telefoon);
        $res = $stmt->execute();
        if ($res) {
            $id = $conn->insert_id;
            return Klant::get($id);
        } else throw new \Exception($stmt->error, 500);
    }

    /**
     * @throws \Exception
     */
    public static function get(int $id): Klant
    {
        return new Klant($id);
    }

    public int $id;

    public string $naam, $adres, $postcode, $woonplaats, $telefoon;

    /**
     * @throws \Exception
     */
    private function __construct(int $id)
    {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM `klanten` WHERE id = ?");
        $stmt->bind_param("i", $id);
        $res = $stmt->execute();
        if ($res) {
            $stmt->bind_result($id, $naam, $adres, $postcode, $woonplaats, $telefoon);
            if ($stmt->fetch() === null)
                throw new \Exception("Klant met id $id bestaat niet", 404);

            $this->id = $id;
            $this->naam = $naam;
            $this->adres = $adres;
            $this->postcode = $postcode;
            $this->woonplaats = $woonplaats;
            $this->telefoon = $telefoon;
        } else throw new \Exception($stmt->error, 500);
    }

    public function save()
    {
        global $conn;
        $stmt = $conn->prepare("UPDATE `klanten` SET naam = ?, adres = ?, postcode = ?, woonplaats = ?, telefoon = ? WHERE id = ?");
        $stmt->bind_param("ssssssi", $this->naam, $this->adres, $this->postcode, $this->woonplaats, $this->telefoon, $this->id);
        if (!$stmt->execute()) throw new \Exception($stmt->error, 500);
    }

    public function delete()
    {
        global $conn;
        $stmt = $conn->prepare("DELETE FROM `klanten` WHERE id = ?");
        $stmt->bind_param("i", $this->id);
        if (!$stmt->execute()) throw new \Exception($stmt->error, 500);
    }

    public function jsonSerialize(): array
    {
        return [
            "id" => $this->id,
            "naam" => $this->naam,
            "adres" => $this->adres,
            "postcode" => $this->postcode,
            "woonplaats" => $this->woonplaats,
            "telefoon" => $this->telefoon,
        ];
    }
}