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
        return $this->columns;
    }

    public function getRows() {
        return $this->rows;
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

        $column_row = array_shift($body);
        $columns = array_map(function ($c) {
            return new Column($c[0], $c[1], []);
        }, $column_row);

        $column_names = array_map(function (Column $c) {
            return $c->getName();
        }, $columns);

        $self = new self();
        $self->columns = $columns;
        $self->rows = [];

        foreach ($body as $row) {
            $self->rows[] = array_combine($column_names, $row);
        }

        return $self;
    }
}