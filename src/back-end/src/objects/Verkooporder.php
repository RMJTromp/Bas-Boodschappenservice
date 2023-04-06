<?php

namespace Boodschappenservice\objects;

use Boodschappenservice\utilities\ArrayList;

/**
 * @property-read int $id
 */
class Verkooporder implements \JsonSerializable {

    public static function getAll() : array {
        global $conn;
        $stmt = $conn->prepare("SELECT verkoopId FROM `verkooporders`");
        $res = $stmt->execute();
        if($res) {
            $verkooporders = new ArrayList();
            $stmt->bind_result($id);
            while($stmt->fetch()) {
                $verkooporders->add($id);
            }

            return $verkooporders->map(function($id) {
                return Verkooporder::get($id);
            })->getArray();
        } else throw new \Exception($stmt->error, 500);
    }

    public static function create(int $klantId, int $artId, int $verkoopAantal, string $verkoopDatum) : Verkooporder {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO `verkooporders` (klantId, artId, verkoopAantal, verkoopDatum) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $klantId, $artId, $verkoopAantal, $verkoopDatum);
        $res = $stmt->execute();
        if($res) {
            $id = $conn->insert_id;
            return Verkooporder::get($id);
        } else throw new \Exception($stmt->error, 500);
    }

    public static function get(int $id) : Verkooporder {
        return new Verkooporder($id);
    }

    public int $verkoopId;
    public int $klantId;
    public int $artId;
    public int $verkoopAantal;
    public string $verkoopDatum;

    private function __construct(int $id) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM `verkooporders` WHERE verkoopId = ?");
        $stmt->bind_param("i", $id);
        $res = $stmt->execute();
        if($res) {
            $stmt->bind_result($verkoopId, $klantId, $artId, $verkoopAantal, $verkoopDatum);
            if($stmt->fetch() === null)
                throw new \Exception("Verkooporder met id $id bestaat niet", 404);

            $this->verkoopId = $verkoopId;
            $this->klantId = $klantId;
            $this->artId = $artId;
            $this->verkoopAantal = $verkoopAantal;
            $this->verkoopDatum = $verkoopDatum;
        } else throw new \Exception($stmt->error, 500);
    }

    public function save() {
        global $conn;
        $stmt = $conn->prepare("UPDATE `verkooporders` SET klantId = ?, artId = ?, verkoopAantal = ?, verkoopDatum = ? WHERE verkoopId = ?");
        $stmt->bind_param("iiiis", $this->klantId, $this->artId, $this->verkoopAantal, $this->verkoopDatum, $this->verkoopId);
        if(!$stmt->execute()) throw new \Exception($stmt->error, 500);
    }

    public function delete() {
        global $conn;
        $stmt = $conn->prepare("DELETE FROM `verkooporders` WHERE verkoopId = ?");
        $stmt->bind_param("i", $this->verkoopId);
        if(!$stmt->execute()) throw new \Exception($stmt->error, 500);
    }

    public function jsonSerialize(): array {
        return [
            "verkoopId" => $this->verkoopId,
            "klantId" => $this->klantId,
            "artId" => $this->artId,
            "verkoopAantal" => $this->verkoopAantal,
            "verkoopDatum" => $this->verkoopDatum
        ];
    }

    public static function searchVerkoopordersByDate(string $searchDate): array {
        global $conn;
        $stmt = $conn->prepare("SELECT verkOrdId FROM `VERKOOPORDERS` WHERE verkOrdDatum = ?");
        $stmt->bind_param("s", $searchDate);
        $res = $stmt->execute();

        if ($res) {
            $orders = new ArrayList();
            $stmt->bind_result($verkOrdId);
            while ($stmt->fetch()) {
                $orders->add($verkOrdId);
            }

            return $orders->map(function ($verkOrdId) {
                return Verkooporders::get($verkOrdId);
            })->getArray();
        } else {
            throw new \Exception($stmt->error, 500);
        }
    }
//$searchResults = Verkooporders::searchVerkoopordersByDate('2023-04-06');
}

