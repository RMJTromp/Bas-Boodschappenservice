<?php

    namespace Boodschappenservice\utilities;

    class Timer implements \JsonSerializable {

        private float $last = 0;

        public float $startTime = 0, $endTime = 0;

        /** @var float[] */
        public array $laps = [];

        public function __construct() {}

        public function start() : void {
            $this->startTime = microtime(true);
            $this->last = $this->startTime;
        }

        public function lap() : int {
            $lap = microtime(true);
            $this->laps[] = $lap;
            $res = Math::round(($lap - $this->last) * 1000);
            $this->last = $lap;
            return $res;
        }

        public function end() : float {
            $end = microtime(true);
            $this->endTime = $end;
            return Math::round(($end - $this->last) * 1000);
        }

        public function jsonSerialize(): mixed {
            return [
                "start" => $this->startTime,
                "laps" => $this->laps,
                "end" => $this->endTime
            ];
        }
    }