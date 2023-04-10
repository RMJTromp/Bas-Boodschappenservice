<?php

    namespace Boodschappenservice\objects;

    use Boodschappenservice\attributes\Column;
    use Boodschappenservice\attributes\ProxyProperty;
    use Boodschappenservice\attributes\Sensitive;
    use Boodschappenservice\attributes\Table;
    use Boodschappenservice\utilities\ArrayList;
    use Exception;
    use ReflectionClass;
    use ReflectionException;
    use ReflectionProperty;

    abstract class BaseObject implements \JsonSerializable {

        /**
         * @return static[]
         * @throws Exception
         */
        public static function getAll(int $limit = -1, int $offset = 0) : array {
            $table = self::getTable();
            /**
             * @var ReflectionProperty $prop
             * @var Column $column
             */
            [$prop, $column] = self::getPrimaryProperty();

            global $conn;
            $stmt = $conn->prepare("SELECT * FROM {$table->name} "  . ($limit > 0 ? " LIMIT {$limit} OFFSET {$offset}" : ""));
            if($stmt->execute()) {
                $res = $stmt->get_result();
                $objects = [];
                while($row = $res->fetch_assoc()) {
                    $object = self::create();
                    foreach($row as $key => $value) {
                        unset($_res, $prop, $column);
                        $_res = self::findProperty(fn(Column $column) => $column->name == $key);
                        if($_res !== null) {
                            [$prop, $column] = $_res;
                            $prop->setValue($object, $value);
                        }
                    }
                    $objects[] = $object;
                }

                return $objects;
            } else throw new Exception($stmt->error, 500);
        }

        public static function count() : int {
            $table = self::getTable();
            global $conn;
            $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM {$table->name}");
            if($stmt->execute()) {
                $res = $stmt->get_result();
                $row = $res->fetch_assoc();
                return intval($row["count"] ?? 0);
            } else throw new Exception($stmt->error, 500);
        }

        public static function random() : ?static {
            $table = self::getTable();

            global $conn;

            $stmt = $conn->prepare("SELECT ROUND(RAND() * (SELECT COUNT(*) FROM {$table->name})) AS offset");
            if($stmt->execute()) {
                $res = $stmt->get_result();
                $row = $res->fetch_assoc();
                if($res->num_rows > 0) {
                    $offset = $row["offset"];

                    $stmt = $conn->prepare("SELECT * FROM {$table->name} LIMIT 1 OFFSET {$offset}");
                    if($stmt->execute()) {
                        $res = $stmt->get_result();
                        $row = $res->fetch_assoc();
                        $object = self::create();
                        foreach($row as $key => $value) {
                            unset($_res, $prop, $column);
                            $_res = self::findProperty(fn(Column $column) => $column->name == $key);
                            if($_res !== null) {
                                [$prop, $column] = $_res;
                                $prop->setValue($object, $value);
                            }
                        }
                        return $object;
                    } else throw new Exception($stmt->error, 500);
                } else return null;
            } else throw new Exception($stmt->error, 500);
        }

        /**
         * @throws ReflectionException
         */
        public static function create() : static {
            $classObj = new ReflectionClass(get_called_class());
            $constructor = $classObj->getConstructor();
            $constructor->setAccessible(true);
            $instance = $classObj->newInstanceWithoutConstructor();
            $constructor->invoke($instance);
            return $instance;
        }

        /**
         * @throws ReflectionException
         */
        public static function get(int $id) : static {
            $classObj = new ReflectionClass(get_called_class());
            $constructor = $classObj->getConstructor();
            $constructor->setAccessible(true);
            $instance = $classObj->newInstanceWithoutConstructor();
            $constructor->invoke($instance, $id);
            return $instance;
        }

        /** @throws Exception */
        protected function __construct(int $id = -1) {
            if($id === -1) return;
            $table = self::getTable();
            /**
             * @var ReflectionProperty $prop
             * @var Column $primaryColumn
             */
            [$prop, $primaryColumn] = self::getPrimaryProperty();

            global $conn;
            $stmt = $conn->prepare("SELECT * FROM {$table->name} WHERE {$primaryColumn->name} = ?");
            $stmt->bind_param("i", $id);
            if($stmt->execute()) {
                $res = $stmt->get_result();
                if($res->num_rows > 0) {
                    $row = $res->fetch_assoc();
                    foreach($row as $key => $value) {
                        unset($res, $prop, $column);
                        $res = self::findProperty(fn(Column $column) => $column->name == $key);
                        if($res !== null) {
                            [$prop, $column] = $res;
                            $prop->setValue($this, $value);
                        }
                    }
                } else {
                    $class = strtolower((new ReflectionClass($this))->getShortName());
                    throw new Exception("{$class} met {$prop->name} #{$id} bestaat niet", 404);
                }
            } else throw new Exception($stmt->error, 500);
        }

        /** @throws Exception */
        public function delete() : void {
            $table = self::getTable();
            /**
             * @var ReflectionProperty $prop
             * @var Column $primaryColumn
             */
            [$prop, $primaryColumn] = self::getPrimaryProperty();

            global $conn;
            $stmt = $conn->prepare("DELETE FROM {$table->name} WHERE {$primaryColumn->name} = ?");
            $value = $prop->getValue($this);
            $stmt->bind_param("i", $value);
            if(!$stmt->execute()) throw new Exception($stmt->error, 500);
            if($conn->error) throw new Exception($conn->error, 500);
        }

        public function save() : void {
            /** @var ReflectionProperty $prop */
            [$prop,] = self::getPrimaryProperty();
            if(!$prop->isInitialized($this)) $this->insert();
            else $this->update();
        }

        public function insert(): void {
            $table = self::getTable();
            /**
             * @var ReflectionProperty $prop
             * @var Column $primaryColumn
             */
            [$prop, $primaryColumn] = self::getPrimaryProperty();

            $nonPrimaryColums = (new ArrayList(self::getProperties()))
                ->map(function(array $arr) {
                    /**
                     * @var ReflectionProperty $prop
                     * @var Column $column
                     */
                    [$prop, $column] = $arr;
                    $type = $prop->getType()->getName();
                    return [
                        "key" => $column->name,
                        "value" => $prop->isInitialized($this) ? $prop->getValue($this) : null,
                        "type" => $type === "int" ? "i" : ($type === "float" ? "d" : "s"),
                        "prop" => $prop,
                        "column" => $column
                    ];
                })
                ->filter(fn(array $prop) => !$prop['column']->primary);

            global $conn;
            $stmt = $conn->prepare("INSERT INTO {$table->name} (". $nonPrimaryColums->map(fn($prop) => $prop['key'])->join(", ") .") VALUES (". $nonPrimaryColums->map(fn($prop) => "?")->join(", ") .")");
            $types = $nonPrimaryColums->map(fn(array $prop) => $prop["type"])->join("");
            $vars = [...$nonPrimaryColums->map(fn(array $prop) => $prop["value"])->getArray()];
            $stmt->bind_param($types, ...$vars);
            if(!$stmt->execute()) throw new Exception($stmt->error, 500);
            $prop->setValue($this, $conn->insert_id);
        }

        /**
         * @throws Exception
         */
        private function update(): void {
            $table = self::getTable();
            /**
             * @var ReflectionProperty $prop
             * @var Column $primaryColumn
             */
            [$prop, $primaryColumn] = self::getPrimaryProperty();

            $nonPrimaryColums = (new ArrayList(self::getProperties()))
                ->map(function(array $arr) {
                    /**
                     * @var ReflectionProperty $prop
                     * @var Column $column
                     */
                    [$prop, $column] = $arr;
                    $type = $prop->getType()->getName();
                    return [
                        "key" => $column->name,
                        "value" => $prop->getValue($this),
                        "type" => $type === "int" ? "i" : ($type === "float" ? "d" : "s"),
                        "prop" => $prop,
                        "column" => $column
                    ];
                })
                ->filter(fn(array $prop) => !$prop['column']->primary);

            global $conn;
            $stmt = $conn->prepare("UPDATE {$table->name} SET " . $nonPrimaryColums->map(fn(array $prop) => $prop["key"])->join(" = ?, ") . " = ?" . " WHERE {$primaryColumn->name} = ?");
            $primaryType = ($primaryType = $prop->getType()->getName()) === "int" ? "i" : ($primaryType === "float" ? "d" : "s");
            $types = $nonPrimaryColums->map(fn(array $prop) => $prop["type"])->join("") . $primaryType;
            $vars = [
                ...$nonPrimaryColums->map(fn(array $prop) => $prop["value"])->getArray(),
                $prop->getValue($this)
            ];
            $stmt->bind_param($types, ...$vars);
            if(!$stmt->execute()) throw new Exception($stmt->error, 500);
        }

        private static function getTable() : Table {
            $class = get_called_class();
            $attributes = (new ReflectionClass($class))->getAttributes(Table::class);
            if(!empty($attributes)) return $attributes[0]->newInstance();
            else throw new Exception("Class $class does not have a #[Table] attribute");
        }

        private static function getPrimaryProperty() : array {
            $res = self::findProperty(fn(Column $column) => $column->primary);
            if($res === null) throw new Exception("No primary key found");
            return $res;
        }

        private static function getProperties(callable $callback = null) : array {
            $class = get_called_class();
            $properties = [];
            foreach((new ReflectionClass($class))->getProperties() as $prop) {
                if($prop->class != $class || $prop->isStatic()) continue;
                if($prop->isPrivate()) $prop->setAccessible(true);
                $attributes = $prop->getAttributes(Column::class);
                if(!empty($attributes)) {
                    $column = $attributes[0]->newInstance();
                    if($callback === null || $callback($column))
                        $properties[] = [$prop, $column];
                }
            }
            return $properties;
        }

        private static function findProperty(callable $callback) : ?array {
            $class = get_called_class();
            foreach((new ReflectionClass($class))->getProperties() as $prop) {
                if($prop->class != $class || $prop->isStatic()) continue;
                if($prop->isPrivate()) $prop->setAccessible(true);
                $attributes = $prop->getAttributes(Column::class);
                if(!empty($attributes)) {
                    $column = $attributes[0]->newInstance();
                    if($callback($column))
                        return [$prop, $column];
                }
            }
            return null;
        }

        public function __get(string $name) {
            $property = new ReflectionProperty(get_class($this), $name);
            if($property->isPrivate()) $property->setAccessible(true);
            return $property->isInitialized($this) ? $property->getValue($this) : null;
        }

        /** @throws Exception */
        public function __set(string $name, $value): void {
            if(property_exists($this, $name)) {
                $property = new ReflectionProperty($this, $name);
                if($property->isPrivate()) $property->setAccessible(true);
                $attributes = $property->getAttributes(Column::class);
                if(!empty($attributes)) {
                    /** @var Column $column */
                    $column = $attributes[0]->newInstance();

                    if($column->immutable) throw new Exception("$name is onveranderlijk", 400);
                    if(!$column->nullable && $value === null) throw new Exception("$name kan niet null zijn", 400);

                    switch ($property->getType()->getName()) {
                        case "string":
                            $value = trim($value);
                            $length = strlen($value);

                            if($length < $column->minLength) throw new Exception("$name moet minimaal $column->minLength tekens lang zijn", 400);
                            if($length > $column->maxLength) throw new Exception("$name mag maximaal $column->maxLength tekens lang zijn", 400);
                            if($column->regexp !== null && !$column->regexp->test($value)) throw new Exception("ongeldige $name opgegeven", 400);
                            break;
                        case "int":
                            if($value < $column->min) throw new Exception("$name moet minimaal $column->min zijn", 400);
                            if($value > $column->max) throw new Exception("$name mag maximaal $column->max zijn", 400);
                            break;
                        default:
                            break;
                    }
                }
                $property->setValue($this, $value);
            }
        }

        public function jsonSerialize(): array {
            $res = [];
            $class = get_class($this);
            (new ArrayList((new ReflectionClass($class))->getProperties()))
                ->filter(fn(ReflectionProperty $property) => $property->class == $class && !str_starts_with($property->getName(), "_"))
                ->map(fn(ReflectionProperty $property) => $property->getName())
                ->forEach(function(string $name) use ($class, &$res) {
                    $property = new ReflectionProperty($class, $name);
                    if($property->getAttributes(Sensitive::class)) return;
                    if($property->isPrivate()) $property->setAccessible(true);

                    $attributes = $property->getAttributes(ProxyProperty::class);
                    if(!empty($attributes)) {
                        /** @var ProxyProperty $proxy */
                        $proxy = $attributes[0]->newInstance();
                        $_property = new ReflectionProperty($class, $proxy->propertyName);
                        if($_property->isPrivate()) $_property->setAccessible(true);
                        $value = $_property->isInitialized($this) ? $_property->getValue($this) : null;
                    } else {
                        $value = $property->isInitialized($this) ? $property->getValue($this) : null;
                    }

                    $res[$name] = $value;
                });
            return $res;
        }

    }