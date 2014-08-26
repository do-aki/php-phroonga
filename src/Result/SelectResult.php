<?php

namespace dooaki\Phroonga\Result;

use dooaki\Phroonga\GroongaResult;
use dooaki\Phroonga\Table;
use dooaki\Phroonga\Column;
use dooaki\Container\Lazy\Enumerable;
use dooaki\Container\Lazy\Enumerator;
use dooaki\Phroonga\Exception\InvalidArgument;
use dooaki\Phroonga\ResultEntity;

class SelectResult extends GroongaResult {
    use Enumerable;

    const RESULT_TYPE_SEARCH    = 1;
    const RESULT_TYPE_DRILLDOWN = 2;

    private $entity_class;
    private $result_type = self::RESULT_TYPE_SEARCH;
    private $found_count = 0;
    private $columns = [];
    private $rows = [];
    private $drilldown_count = 0;
    private $drilldown_columns = [];
    private $drilldown_rows = [];

    public function setEntityClass($entity_class) {
        $this->entity_class = $entity_class;
    }

    public function getFoundCount() {
        return $this->found_count;
    }

    public function getColumns() {
        foreach ($this->columns as $column) {
            yield new Column($column[0], $column[1], []);
        }
    }

    public function getRows() {
        $cls = $this->entity_class;
        $column_names = (new Enumerator($this->getColumns()))
            ->map(function (Column $c){ return $c->getName(); })->toArray();

        foreach ($this->rows as $row) {
            yield $cls::restore(array_combine($column_names, $row));
        }
    }

    public function getDrilldownCount() {
        return $this->drilldown_count;
    }

    public function getDrilldownColumns() {
        foreach ($this->drilldown_columns as $column) {
            yield new Column($column[0], $column[1], []);
        }
    }

    public function getDrilldownRows() {

        list($columns, $column_names) = (new Enumerator($this->getDrilldownColumns()))
            ->map(function (Column $c) { return [$c, $c->getName()];})
            ->transpose()
            ->toArray();

        foreach ($this->drilldown_rows as $row) {
            $result = new ResultEntity($columns);
            yield $result->setArray(array_combine($column_names, $row));
        }
    }

    public function each(callable $callback = null) {

        if ($this->result_type === self::RESULT_TYPE_SEARCH) {
            $enum = $this->getRows();
        } elseif ($this->result_type === self::RESULT_TYPE_DRILLDOWN) {
            $enum = $this->getDrilldownRows();
        } else {
            throw new InvalidArgument("invalid result type '{$this->result_type}'");
        }

        if ($callback === null) {
            return $enum;
        }

        foreach ($enum as $k => $v) {
            call_user_func($callback, $v, $k);
        }
    }

    public function eachSearchResult() {
        $this->result_type = self::RESULT_TYPE_DRILLDOWN;
        return $this;
    }

    public function eachDrilldownResult() {
        $this->result_type = self::RESULT_TYPE_DRILLDOWN;
        return $this;
    }

    public static function fromArray(array $result) {
        $self = parent::fromArray($result);
        $body = $self->getBody();
        if (!$self->isSuccess()) {
            return $self;
        }

        $search_result = array_shift($body);
        $self->found_count = array_shift($search_result);
        $self->columns = array_shift($search_result);
        $self->rows = $search_result;

        $drilldown_result = array_shift($body);
        if ($drilldown_result) {
            $self->drilldown_count = array_shift($drilldown_result);
            $self->drilldown_columns = array_shift($drilldown_result);
            $self->drilldown_rows = $drilldown_result;
        }

        return $self;
    }
}