<?php

namespace dooaki\Phroonga\Result;

use dooaki\Phroonga\GroongaResult;
use dooaki\Phroonga\Table;
use dooaki\Phroonga\Column;

class SelectResult extends GroongaResult {
    private $entity_class;
    private $found_count;
    private $columns = [];
    private $rows = [];

    public function setEntityClass($entity_class) {
        $this->entity_class = $entity_class;
    }

    public function getColumns() {
        return $this->columns;
    }

    public function getRows() {
        $cls = $this->entity_class;
        foreach ($this->rows as $row) {
            yield $cls::__set_state($row);
        }
    }

    public function getFoundCount() {
        return $this->found_count;
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
        $self = parent::fromArray($result);
        $body = $self->getBody();
        if (!$self->isSuccess()) {
            return $self;
        }

        $body = array_shift($body);

        $found_count = array_shift($body)[0];

        $column_row = array_shift($body);
        $columns = array_map(function ($c) {
            return new Column($c[0], $c[1], []);
        }, $column_row);

        $column_names = array_map(function (Column $c) {
            return $c->getName();
        }, $columns);

        $self->found_count = $found_count;
        $self->columns = $columns;
        $self->rows = [];
        foreach ($body as $row) {
            $self->rows[] = array_combine($column_names, $row);
        }

        return $self;
    }
}