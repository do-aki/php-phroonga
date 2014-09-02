<?php
namespace dooaki\Phroonga;

use dooaki\Phroonga\Exception\ColumnNotfound;
use dooaki\Phroonga\Exception\CommandFailure;

class Table
{
    private $name;
    private $options;
    private $columns = [];

    public function __construct($name, array $options)
    {
        $this->name = $name;
        $this->options = $options;
        if (isset($this->options['key_type'])) {
            $this->addColumn(
                new Column('_key', $this->options['key_type'], ['flags' => 'COLUMN_SCALAR'])
            );
        }
    }

    public function hasKey()
    {
        return !(isset($this->options['flags']) && false !== strpos($this->options['flags'], 'TABLE_NO_KEY'));
    }

    public function getKeyName()
    {
        return $this->hasKey() ? '_key' : '_id';
    }

    public function getName()
    {
        return $this->name;
    }

    public function addColumn(Column $cd)
    {
        $this->columns[$cd->getName()] = $cd;
    }

    /**
     *
     * @param string $name
     * @throws ColumnNotfound
     * @return \dooaki\Phroonga\Column
     */
    public function getColumn($name)
    {
        if ($this->tryGetColumn($name) === null) {
            throw new ColumnNotFound("column '{$name}' is not defined");
        }
        return $this->columns[$name];
    }

    /**
     *
     * @param string $name
     * @param string $default
     * @return \dooaki\Phroonga\Column
     */
    public function tryGetColumn($name, $default = null) {
        return isset($this->columns[$name]) ? $this->columns[$name] : $default;
    }

    public function getColumns()
    {
        return array_values($this->columns);
    }

    public function createTable(DriverInterface $driver)
    {
        $result = $driver->tableCreate($this->name, $this->options);
        if ($result->isFailure()) {
            $e = new CommandFailure($result->getErrorMessage());
            $e->setResult($result);
            throw $e;
        }
    }

    public function createColumns(DriverInterface $driver)
    {
        foreach ($this->columns as $cd) {
            $cd->createColumn($driver, $this);
        }
    }

    public function removeTable(DriverInterface $driver)
    {
        $result = $driver->tableRemove($this->name);
        if ($result->isFailure()) {
            $e = new CommandFailure($result->getErrorMessage());
            $e->setResult($result);
            throw $e;
        }
    }

    public function removeColumns(DriverInterface $driver)
    {
        foreach ($this->columns as $cd) {
            $cd->removeColumn($driver, $this);
        }
    }
}
