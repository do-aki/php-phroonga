<?php

namespace dooaki\Phroonga {

    use dooaki\Phroonga\Exception\DriverNotFound;
    use dooaki\Phroonga\Exception\InvalidEntity;

    class Groonga {
        private $driver;
        private $entities = [];

        public function __construct($host, $port, $options = []) {
            $options += [
                'driver' => Driver\Http::class
            ];
            $this->setDriver($options['driver']);

            $this->driver->connect($host, $port);
        }

        public function setDriver($driver) {
            if ($driver instanceof DriverInterface) {
                $this->driver = $driver;
            } elseif (is_string($driver)) {
                if (class_exists($driver) && is_a($driver, DriverInterface::class, true)) {
                    $this->driver = new $driver();
                } else {
                    throw new DriverNotFound("{$driver} is not DriverInterface");
                }
            }

            if (!$this->driver) {
                throw new DriverNotFound();
            }
        }

        public function activate(array $classes = null) {
            $entity_trait_name = GroongaEntity::class;
            if ($classes === null && trait_exists($entity_trait_name, false)) {
                $classes = [];
                foreach (get_declared_classes() as $cls) {
                    if (in_array($entity_trait_name, class_uses($cls, false))) {
                        $classes[] = $cls;
                    }
                }
            }

            foreach ($classes as $cls) {
                $this->activateClass($cls);
            }
        }

        protected function activateClass($cls) {
            $entitiy_name = $cls::_activate($this);
            if ($entitiy_name === null) {
                $entitiy_name = static::classToEntitiyName($cls);
            }

            $this->entities[$entitiy_name] = $cls;
        }

        public function tables() {
            return $this->driver->tableList();
        }

        public function create() {
            $target = array_map(function ($cls) {
                return SchemaMapping::getTable($cls);
            }, $this->entities);

            $tables = array_column($this->driver->tableList()->getRows(), 'name');
            $target = array_filter($target, function ($td) use($tables) {
                return !in_array($td->getName(), $tables);
            });

            foreach ($target as $td) {
                $td->createTable($this->driver);
            }

            foreach ($target as $td) {
                $td->createColumns($this->driver);
            }
        }

        public function save($entity) {
            if ($entity === null) {
                new InvalidEntity("cannot save entity");
            }

            $cls = get_class($entity);

            var_dump($this->driver->load(SchemaMapping::getTable($cls)->getName(), $entity->toJson()));
        }

        /**
         *
         * @param string $entity_class
         * @return Query
         */
        public function select($entity_class) {
            $q = new Query($this->driver);
            $q->setEntityClass($entity_class);
            return $q;
        }

        public static function classToEntitiyName($cls) {
            $pos = strrpos($cls, '\\');
            if ($pos === false) {
                return $cls;
            }

            return substr($cls, $pos + 1);
        }
    }
}

namespace dooaki\Phroonga\Exception {

    class DriverNotFound extends \Exception {
    }
    class TableNotfound extends \Exception {
    }
    class ColumnNotfound extends \Exception {
    }
    class InvalidType extends \Exception {
    }
    class InvalidEntity extends \Exception {
    }
    class InvalidReferenceKey extends \Exception {}

}