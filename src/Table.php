<?php

namespace dooaki\Phroonga;

use dooaki\Phroonga\Exception\ColumnNotfound;

class Table {
    private $name;
    private $options;
    private $columns = [];

    public function __construct($name, array $options) {
        $this->name = $name;
        $this->options = $options;
        $this->addColumn(new Column('_id', 'Uint32', [
            'flags' => 'COLUMN_SCALAR'
        ]));
        if (isset($this->options['key_type'])) {
            $this->addColumn(new Column('_key', $this->options['key_type'], [
                'flags' => 'COLUMN_SCALAR'
            ]));
        }
    }

    public function hasKey() {
        return !(isset($this->options['flags']) && false !== strpos($this->options['flags'], 'TABLE_NO_KEY'));
    }

    public function getName() {
        return $this->name;
    }

    public function addColumn(Column $cd) {
        $this->columns[$cd->getName()] = $cd;
    }

    /**
     *
     * @param unknown $name
     * @throws ColumnNotfound
     * @return Column
     */
    public function getColumn($name) {
        if (!isset($this->columns[$name])) {
            throw new ColumnNotfound("column '{$name}' is not defined");
        }
        return $this->columns[$name];
    }

    public function getColumns() {
        return array_values($this->columns);
    }

    public function createTable(DriverInterface $driver) {
        $driver->tableCreate($this->name, $this->options);
    }

    public function createColumns(DriverInterface $driver) {
        foreach ($this->columns as $cd) {
            $cd->createColumn($driver, $this);
        }
    }

    public function removeTable(DriverInterface $driver) {
        $driver->tableRemove($this->name);
    }

    public function removeColumns(DriverInterface $driver) {
        foreach ($this->columns as $cd) {
            $cd->removeColumn($driver, $this);
        }
    }

}
