<?php

    namespace Boodschappenservice\utilities;

    /*
     * UUID Format: hex, 8-4-4-4-12. 32 digits
     */
    class UUID implements \JsonSerializable {

        /** @var string[] */
        private array $uuid = [];

        private function __construct(array $uuid) {
            $this->uuid = $uuid;
        }

        public function __toString() {
            return implode('-', $this->uuid);
        }

        /** @return string[] */
        public function getArray() : array {
            return $this->uuid;
        }

        public function getFormatted() : string {
            return (string)$this;
        }

        public function getUnformatted() : string {
            return implode('', $this->uuid);
        }

        public static function randomUUID() : UUID {
            return UUID::fromString(sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff)
            ));
        }

        public static function fromString(string $string) : UUID {
            // Check format
            $matches = null;
            if(preg_match('/^([a-f0-9]{8})\-?([a-f0-9]{4})\-?([a-f0-9]{4})\-?([a-f0-9]{4})\-?([a-f0-9]{12})$/i', $string, $matches) == 1) {
                // Remove 1st value (full subject)
                array_shift($matches);
                return new UUID($matches);
            } else {
                throw new \InvalidArgumentException('The given string is no valid UUID.');
            }
        }

        public function jsonSerialize() : string {
            return (string) $this;
        }
    }