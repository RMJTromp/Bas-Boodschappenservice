<?php

    namespace Boodschappenservice\utilities;

    /**
     * @property-read ArrayList $companies
     * @property-read ArrayList $names
     * @property-read ArrayList $plaats
     * @property-read ArrayList $street
     * @property-read ArrayList $surnames
     */
    class MockData {

        private ArrayList $companies, $names, $plaats, $street, $surnames;

        private static ?MockData $instance = null;

        public static function getInstance() : MockData {
            if(!isset(self::$instance)) {
                self::$instance = new MockData();
            }
            return self::$instance;
        }

        private function __construct() {}

        public function __get($name) {
            if(property_exists($this, $name)) {
                if(!isset($this->$name)) {
                    $file = new File("mock_data/{$name}.json");
                    $arr = JSON::decode($file);
                    $list = new ArrayList($arr);
                    $this->$name = $list;
                }

                return $this->$name;
            }
        }

        public static function strip (string $string) : string {
            return RegExp::compile("/[^\w]/u")->replace("", $string);
        }

    }