<?php

    namespace Boodschappenservice\utilities;

    use ArrayAccess;
    use Countable;
    use IteratorAggregate;
    use Boodschappenservice\exceptions\ArrayOutOfBoundsException;
    use Serializable;

    class ArrayList implements IteratorAggregate, ArrayAccess, Serializable, Countable, \JsonSerializable {

        private array $array = [];

        public function __construct(array $array = []) {
            $this->addAll($array);
        }

        public function getIterator() {
            return new \ArrayObject($this->array);
        }

        public function getArray() : array {
            return $this->array;
        }

        public function offsetExists($offset) : bool {
            if(!is_integer($offset)) return false;
            return isset($this->array[$offset]);
        }

        public function offsetGet($offset) : mixed {
            if(!$this->offsetExists($offset)) throw new ArrayOutOfBoundsException("Index: $offset, Size: {$this->count()}");
            return $this->array[$offset];
        }

        public function offsetSet($offset, $value) : void {
            if(!$this->offsetExists($offset)) throw new ArrayOutOfBoundsException("Index: $offset, Size: {$this->count()}");
            $this->array[$offset] = $value;
        }

        public function offsetUnset($offset) : void {
            if(!$this->offsetExists($offset)) throw new ArrayOutOfBoundsException("Index: $offset, Size: {$this->count()}");
            unset($this->array[$offset]);
        }

        public function serialize() : string {
            return serialize($this->array);
        }

        public function unserialize($data) {
            return $this->unserialize($data);
        }

        public function count() : int {
            return count($this->array);
        }

        public function get(int $index) : mixed {
            return $this->offsetGet($index);
        }

        public function getOrElse(int $index, mixed $alternative) : mixed {
            if($this->offsetExists($index)) return $this->offsetGet($index);
            else return $alternative;
        }

        public function getLastOrElse(mixed $alternative) : mixed {
            return $this->getOrElse($this->count() - 1, $alternative);
        }

        public function add(mixed $object) : ArrayList {
            array_push($this->array, $object);
            return $this;
        }

        public function addAll(...$objects) : ArrayList {
            foreach ($objects as $object) {
                if(is_array($object)) {
                    foreach ($object as $o) $this->add($o);
                } else $this->add($object);
            }
            return $this;
        }

        public function map(callable $callback) : ArrayList {
            return new ArrayList(array_map($callback, $this->array));
        }

        public function filter(callable $callback) : ArrayList {
            return new ArrayList(array_filter($this->array, $callback));
        }

        public function splice(int $offset, int $length = null) : ArrayList {
            return new ArrayList(array_splice($this->array, $offset, $length));
        }

        public function forEach(callable $callback) : ArrayList {
            foreach ($this->array as $item) $callback($item);
            return $this;
        }

        public function slice(int $offset, int $length = null) : ArrayList {
            return new ArrayList(array_slice($this->array, $offset, $length));
        }

        public function find(mixed $object) : false|int|string {
            return array_search($object, $this->array);
        }

        public function anyMatch(callable $callback) : mixed {
            foreach ($this->array as $item)
                if($callback($item)) return $item;
            return false;
        }

        public function contains(mixed $object) : bool {
            return $this->find($object) !== false;
        }

        public function some(mixed $object) : bool {
            return $this->find($object) !== false;
        }

        public function pop() : ArrayList {
            $this->array = array_pop($this->array);
            return $this;
        }

        public function shift() : ArrayList {
            $this->array = array_shift($this->array);
            return $this;
        }

        public function unique() : ArrayList {
            return new ArrayList(array_unique($this->array));
        }

        public function jsonSerialize() : mixed {
            return $this->array;
        }

        public function __serialize() {
            return serialize($this->array);
        }

        public function __unserialize($data): void {
            $this->array = unserialize($data);
        }

        public function join(string $string) : string {
            return implode($string, $this->array);
        }

        public function random() : mixed {
            return $this->array[array_rand($this->array)];
        }

    }