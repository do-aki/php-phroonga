<?php

namespace dooaki\Phroonga\Result;

use dooaki\Phroonga\GroongaResult;
use dooaki\Phroonga\Column;
use dooaki\Container\Lazy\Enumerable;

class TableListResult extends GroongaResult {
    use Enumerable;

    private $columns = [];
    private $rows = [];

    public function getColumns() {
        foreach ($this->columns as $column) {
            yield new Column($column[0], $column[1], []);
        }
    }

    public function getRows() {
        $enum_column = new Enumerator($this->getColumns());
        $columns = $enum_column->toArray();
        $column_names = $enum_column->map(function (Column $c){ return $c->getName(); })->toArray();

        foreach ($this->rows as $row) {
            yield (new ResultEntity($columns))->setArray(array_combine($column_names, $row));
        }
    }

    public function each(callable $callback = null) {
        if ($callback === null) {
            return $this->getRows();
        } else {
            foreach ($this->getRows() as $row) {
                call_user_func($callback, $row);
            }
        }
    }

    public static function fromArray(array $result) {
        $r = parent::fromArray($result);
        $body = $r->getBody();

        $self = new self();
        $self->columns = array_shift($body);
        $self->rows    = $body;

        return $self;
    }
}