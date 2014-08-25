<?php

namespace dooaki\Phroonga;

use dooaki\Phroonga\DriverInterface;

class Query {
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

    public function __construct(DriverInterface $driver) {
        $this->driver = $driver;
    }

    public function setEntityClass($entity_class) {
        $this->entity_class = $entity_class;
    }

    public function getTableName() {
        return SchemaMapping::getTable($this->entity_class)->getName();
    }

    public function filter() {
        return $this;
    }

    public function match(array $columns, $word) {
        $this->match_columns = $columns;
        $this->query = $this->escape($word);
    }

    public function query($expr) {
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

    public function offset($offset) {
        $this->offset = intval($offset);
        return $this;
    }

    public function limit($limit) {
        $this->limit = intval($limit);
        return $this;
    }

    public function sortby(array $columns) {
        $this->sortby = $columns;
        return $this;
    }

    public function drilldown(array $columns) {
        $ths->drilldown = $columns;
        return $this;
    }

    public function drilldown_sortby(array $columns) {
        $ths->drilldown_sortby = $columns;
        return $this;
    }

    public function drilldown_output_columns(array $columns) {
        $ths->drilldown_output_columns = $columns;
        return $this;
    }

    public function drilldown_offset($offset) {
        $this->drilldown_offset = intval($offset);
        return $this;
    }

    public function drilldown_limit($limit) {
        $this->drilldown_limit = intval($limit);
        return $this;
    }

    public function findOne($column) {
        $this->output_columns = [
            $column
        ];
        $row = $this->findFirst();
        if (!$row) {
            return null;
        }
        return $row->$column;
    }

    public function findFirst() {
        $this->limit = 1;
        $r = $this->driver->select($this->getTableName(), $this->build());
        $r->setEntityClass($this->entity_class);

        $ret = null;
        foreach ($r->getRows() as $row) {
            $ret = $row;
            break;
        }
        return $ret;
    }

    /**
     *
     * XXX: groonga の default limit 分しか取得できない
     *
     * @return dooaki\Phroonga\Result\SelectResult
     */
    public function findAll() {
        $r = $this->driver->select($this->getTableName(), $this->build());
        $r->setEntityClass($this->entity_class);
        return $r;
    }

    public function pagerize() {
        $r = $this->driver->select($this->getTableName(), $this->build());
        $r->setEntityClass($this->entity_class);
        return $r;
    }

    public function escape($str) {
        return str_replace([
            ' ','"',"'",'(',')','\\'
        ], [
            '\\ ','\\"',"\\'",'\\(','\\)','\\\\'
        ], $str);
    }

    private function build() {
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

    public function __toString() {
        $ret = "select {$this->getTableName()}";
        foreach ($this->build() as $n => $v) {
            $ret .= " --{$n} {$v}";
        }
        ;
        return $ret;
    }
}
