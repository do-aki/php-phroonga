<?php
namespace dooaki\Phroonga;

use dooaki\Phroonga\Exception\DriverNotFound;
use dooaki\Phroonga\Exception\InvalidEntity;
use dooaki\Phroonga\Exception\InvalidArgument;
use dooaki\Phroonga\Driver\Http;

class Groonga
{
    private $driver;

    private $connected = false;

    private $entities = [];

    private $default_drivers = [
        'http' => 'dooaki\Phroonga\Driver\Http',
        'mock' => 'dooaki\Phroonga\Driver\Mock',
    ];

    public function __construct($dsn_or_driver, $options = [])
    {
        if (is_string($dsn_or_driver)) {
            $driver = $this->createDriverFromDsn($dsn_or_driver, $options);
        } elseif ($dsn_or_driver instanceof DriverInterface) {
            $driver = $dsn_or_driver;
            $driver->setOptions($options);
        }

        $this->setDriver($driver);
    }

    public function getDriver()
    {
        return $this->driver;
    }

    public function setDriver(DriverInterface $driver)
    {
        $this->driver = $driver;
    }

    public function activate(array $classes = null)
    {
        $entity_trait_name = GroongaEntity::class;
        if ($classes === null && trait_exists($entity_trait_name, false)) {
            $classes = [];
            foreach (get_declared_classes() as $cls) {
                if (in_array($entity_trait_name, class_uses($cls, false))) {
                    $classes[] = $cls;
                }
            }
        }

        $this->driver->connect();

        foreach ($classes as $cls) {
            $this->activateClass($cls);
        }
    }

    protected function activateClass($cls)
    {
        $entitiy_name = $cls::_activate($this);
        if ($entitiy_name === null) {
            $entitiy_name = static::classToEntitiyName($cls);
        }

        $this->entities[$entitiy_name] = $cls;
    }

    public function status()
    {
        return $this->driver->status();
    }

    public function tables()
    {
        return $this->driver->tableList()->map(
            function (ResultEntity $row) {
                $table = new Table(
                    $row->name,
                    [
                        'flags' => $row->flags,
                        'key_type' => $row->domain,
                        'value_type' => $row->range,
                        'default_tokenizer' => $row->default_tokenizer,
                        'normalizer' => $row->normalizer
                    ]
                );

                $this->driver->columnList($row->name)->map(
                    function (ResultEntity $row) {
                        return new Column(
                            $row->name,
                            $row->type,
                            [
                                'flags' => $row->flags,
                                'source' => $row->source,
                            ]
                        );
                    }
                )->apply(
                    function (Column $c) use ($table) {
                        $table->addColumn($c);
                    }
                );

                return $table;
            }
        );
    }

    public function tableNames()
    {
        return $this->driver->tableList()->map(
            function (ResultEntity $r) {
                return $r->name;
            }
        )->toArray();
    }

    public function create()
    {
        $target = array_map(
            function ($cls) {
                return SchemaMapping::getTable($cls);
            },
            $this->entities
        );

        $tables = $this->tableNames();
        $target = array_filter(
            $target,
            function ($td) use($tables) {
                return !in_array($td->getName(), $tables);
            }
        );

        foreach ($target as $td) {
            $td->createTable($this->driver);
        }

        foreach ($target as $td) {
            $td->createColumns($this->driver);
        }
    }

    public function save($entity)
    {
        if ($entity === null) {
            new InvalidEntity("cannot save entity");
        }

        $cls = get_class($entity);

        // TODO: 直接 load を呼ばずに、変更をバッファリンクする機構が欲しい
        return $this->driver->load(SchemaMapping::getTable($cls)->getName(), $entity->toJsonForLoad());
    }

    /**
     *
     * @param string $entity_class
     * @return Query
     */
    public function select($entity_class)
    {
        $q = new Query($this->driver);
        $q->setEntityClass($entity_class);
        return $q;
    }

    public static function classToEntitiyName($cls)
    {
        $pos = strrpos($cls, '\\');
        if ($pos === false) {
            return $cls;
        }

        return substr($cls, $pos + 1);
    }

    public static function parseDsn($dsn) {
        if (preg_match('#\A([^:]*)://([^/:]*)(:([^/]*))?(/.*)?\z#', $dsn, $m)) {
            $m += array_fill(0, 6, '');
            return [
                'protocol' => $m[1],
                'host' => $m[2],
                'port' => $m[4],
                'path' => $m[5],
            ];
        }
    }

    public function createDriverFromDsn($dsn, array $options) {

        $spec = self::parseDsn($dsn);
        if (!$spec) {
            throw new InvalidArgument("invalid DSN '{$dns}'");
        }

        if (!isset($this->default_drivers[$spec['protocol']])) {
            throw new DriverNotFound("undefined protocol: {$spec['protocol']}");
        }

        $cls = $this->default_drivers[$spec['protocol']];
        $driver = new $cls();
        $driver->setOptions($options + $spec);
        return $driver;
    }
}
