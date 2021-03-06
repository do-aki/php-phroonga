<?php
namespace dooaki\Phroonga;

use dooaki\Phroonga\DriverInterface;
use dooaki\Phroonga\SchemaMapping;

class Query
{
    private $driver;
    private $entity_class;
    private $match_columns;
    private $query;
    private $filter;
    private $scorer;
    private $sortby;
    private $output_columns;
    private $offset;
    private $limit;
    private $drilldown;
    private $drilldown_sortby;
    private $drilldown_output_columns;
    private $drilldown_offset;
    private $drilldown_limit;
    private $cache;
    private $match_escalation_threshold;
    private $query_flags;
    private $query_expander;
    private $adjuster;

    public function __construct(DriverInterface $driver)
    {
        $this->driver = $driver;
    }

    public function setEntityClass($entity_class)
    {
        $this->entity_class = $entity_class;
    }

    public function getTableDefinition()
    {
        return SchemaMapping::getTable($this->entity_class);
    }

    public function getTableName()
    {
        return $this->getTableDefinition()->getName();
    }

    public function filter($filter)
    {
        $this->filter = $filter;
        return $this;
    }

    public function match(array $columns, $word)
    {
        $this->match_columns = $columns;
        $this->query = $word;
        return $this;
    }

    public function query($expr)
    {
        $pos = strpos($expr, '?');
        if ($pos === false) {
            $this->query = $expr;
            return $this;
        }

        $args = func_get_args();
        array_shift($args); // remove $expr

        $built = substr($expr, 0, $pos);
        $offset = $pos;
        while (($pos = strpos($expr, '?', $offset)) !== false) {
            $built .= substr($expr, $offset, $pos - $offset);
            $built .= $this->escape(array_shift($args));

            $offset = $pos + 1;
        }
        $built .= substr($expr, $offset);
        $this->query = $built;

        return $this;
    }

    public function offset($offset)
    {
        $this->offset = intval($offset);
        return $this;
    }

    public function limit($limit)
    {
        $this->limit = intval($limit);
        return $this;
    }

    public function sortby(array $columns)
    {
        $this->sortby = $columns;
        return $this;
    }

    public function output_columns(array $columns) {
        $this->output_columns = $columns;
        return $this;
    }

    public function drilldown(array $columns)
    {
        $this->drilldown = $columns;
        return $this;
    }

    public function drilldown_sortby(array $columns)
    {
        $this->drilldown_sortby = $columns;
        return $this;
    }

    public function drilldown_output_columns(array $columns)
    {
        $this->drilldown_output_columns = $columns;
        return $this;
    }

    public function drilldown_offset($offset)
    {
        $this->drilldown_offset = intval($offset);
        return $this;
    }

    public function drilldown_limit($limit)
    {
        $this->drilldown_limit = intval($limit);
        return $this;
    }

    public function findOne($column)
    {
        $this->output_columns([$column]);
        $row = $this->findFirst();
        return $row ? $row->$column : null;
    }

    public function findFirst()
    {
        $this->limit(1);
        $r = $this->driver->select($this->getTableName(), $this->build());
        $r->setEntityClass($this->entity_class);
        return $r->first();
    }

    public function find($key)
    {
        $key_name = $this->getTableDefinition()->getKeyName();
        $this->query("{$key_name}:?", $key);
        return $this->findFirst();
    }

    /**
     *
     * XXX: 件数多くても一度に全てフェッチしてしまう
     *
     * @return \dooaki\Phroonga\Result\SelectResult
     */
    public function findAll()
    {
        if (!isset($this->limit)) {
            $this->limit(0);
            $r = $this->driver->select($this->getTableName(), $this->build());
            $this->limit($r->getFoundCount());
        }

        $r = $this->driver->select($this->getTableName(), $this->build());
        $r->setEntityClass($this->entity_class);
        return $r;
    }

    public function escape($str)
    {
        return str_replace(
            [' '  , '"'  , "'"  , '('  , ')'  , '\\'  ],
            ['\\ ', '\\"', "\\'", '\\(', '\\)', '\\\\'],
            $str
        );
    }

    private function build()
    {
        $q = [];
        $parameters = [
            'match_columns'              => 'a',
            'query'                      => 's',
            'filter'                     => 's',
            'scorer'                     => 's',
            'sortby'                     => 'a',
            'output_columns'             => 'a',
            'offset'                     => 's',
            'limit'                      => 's',
            'drilldown'                  => 'a',
            'drilldown_sortby'           => 'a',
            'drilldown_output_columns'   => 'a',
            'drilldown_offset'           => 's',
            'drilldown_limit'            => 's',
            'cache'                      => 's',
            'match_escalation_threshold' => 's',
            'query_flags'                => 's',
            'query_expander'             => 's',
            'adjuster'                   => 's'
        ];

        foreach ($parameters as $prop => $type) {
            if (isset($this->$prop)) {
                if ($type === 'a') {
                    $q[$prop] = implode(',', $this->$prop);
                } else {
                    $q[$prop] = $this->$prop;
                }
            }
        }
        return $q;
    }

    public function __toString()
    {
        $ret = "select {$this->getTableName()}";
        foreach ($this->build() as $n => $v) {
            $ret .= " --{$n} {$v}";
        }
        return $ret;
    }
}
