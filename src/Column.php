<?php
namespace dooaki\Phroonga;

class Column
{
    private $name;

    private $flags;

    private $type;

    private $source;

    public function __construct($name, $type, array $options)
    {
        $this->name = $name;
        $this->type = $type;
        $options += [
            'flags' => 'COLUMN_SCALAR',
            'source' => null
        ];
        $this->flags = $options['flags'];
        $this->source = $options['source'];
    }

    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return ($this->type instanceof TableReference) ? $this->type->asType() : $this->type;
    }

    public function getFlags()
    {
        return $this->flags;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function getValidator()
    {
        if ($this->type instanceof TableReference) {
        }

        switch ($this->type) {
            case 'Int16':
            case 'Int32':
            // TODO:
        }
    }

    public function getAdapter()
    {
    }

    public function createColumn(DriverInterface $driver, Table $td)
    {
        if ($this->name[0] === '_') {
            return; // skip reserved name
        }

        $driver->columnCreate($td->getName(), $this->getName(), $this->getFlags(), $this->getType(), $this->getSource());
    }

    public function removeColumn(DriverInterface $driver, Table $td)
    {
        if ($this->name[0] === '_') {
            return; // skip reserved name
        }

        $driver->columnRemove($td->getName(), $this->getName());
    }
}
