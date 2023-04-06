<?php

namespace Boodschappenservice\objects;

use Boodschappenservice\utilities\ArrayList;

/**
 * @property-read int $inkoopId
 */
class Inkooporder implements \JsonSerializable {

    /**
     * @return Array<Inkooporder>
     * @throws \Exception
     */
    public static function getAll() : array {
        global $conn;
        $stmt = $conn->prepare("SELECT inkoopId FROM `inkooporders`");
        $res = $stmt->execute();
        if($res) {
            $inkooporders = new ArrayList();
            $stmt->bind_result($inkoopId);
            while($stmt->fetch()) {
                $inkooporders->add($inkoopId);
            }

            return $inkooporders->map(function($inkoopId) {
                return Inkooporder::get($inkoopId);
            })->getArray();
        } else throw new \Exception($stmt->error, 500);
    }

    /**
     * @throws \Exception
     */
    public static function create(int $artId, int $levId, int $inkoopAantal, string $inkoopDatum) : Inkooporder {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO `inkooporders` (artId, levId, inkoopAantal, inkoopDatum) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $artId, $levId, $inkoopAantal, $inkoopDatum);
        $res = $stmt->execute();
        if($res) {
            $inkoopId = $conn->insert_id;
            return Inkooporder::get($inkoopId);
        } else throw new \Exception($stmt->error, 500);
    }

    /**
     * @throws \Exception
     */
    public static function get(int $inkoopId) : Inkooporder {
        return new Inkooporder($inkoopId);
    }

    public int $inkoopId;

    public int $artId, $levId, $inkoopAantal;
    public string $inkoopDatum;

    /**
     * @throws \Exception
     */
    private function __construct(int $inkoopId) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM `inkooporders` WHERE inkoopId = ?");
        $stmt->bind_param("i", $inkoopId);
        $res = $stmt->execute();
        if($res) {
            $stmt->bind_result($inkoopId, $artId, $levId, $inkoopAantal, $inkoopDatum);
            if($stmt->fetch() === null)
                throw new \Exception("Inkooporder with inkoopId $inkoopId does not exist", 404);

            $this->inkoopId = $inkoopId;
            $this->artId = $artId;
            $this->levId = $levId;
            $this->inkoopAantal = $inkoopAantal;
            $this->inkoopDatum = $inkoopDatum;
        } else throw new \Exception($stmt->error, 500);
    }

    public function save() {
        global $conn;
        $stmt = $conn->prepare("UPDATE `inkooporders` SET artId = ?, levId = ?, inkoopAantal = ?, inkoopDatum = ? WHERE inkoopId = ?");
        $stmt->bind_param("iiisi", $this->artId, $this->levId, $this->inkoopAantal, $this->inkoopDatum, $this->inkoopId);
        if (!$stmt->execute()) throw new \Exception($stmt->error, 500);
    }

    public function delete() {
        global $conn;
        $stmt = $conn->prepare("DELETE FROM `inkooporders` WHERE inkoopId = ?");
        $stmt->bind_param("i", $this->inkoopId);
        if (!$stmt->execute()) throw new \Exception($stmt->error, 500);
    }

    public function jsonSerialize(): array {
        return [
            "inkoopId" => $this->inkoopId,
            "artId" => $this->artId,
            "levId" => $this->levId,
            "inkoopAantal" => $this->inkoopAantal,
            "inkoopDatum" => $this->inkoopDatum
        ];
    }
    public static function searchInkoopordersByStatus(int $searchStatus): array {
        global $conn;
        $stmt = $conn->prepare("SELECT inkOrdId FROM `INKOOPORDERS` WHERE inkOrdStatus = ?");
        $stmt->bind_param("i", $searchStatus);
        $res = $stmt->execute();

        if ($res) {
            $orders = new ArrayList();
            $stmt->bind_result($inkOrdId);
            while ($stmt->fetch()) {
                $orders->add($inkOrdId);
            }

            return $orders->map(function ($inkOrdId) {
                return Inkooporders::get($inkOrdId);
            })->getArray();
        } else {
            throw new \Exception($stmt->error, 500);
        }
    }
    //$searchResults = Inkooporders::searchInkoopordersByStatus(1);
}

