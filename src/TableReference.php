<?php
namespace dooaki\Phroonga;

class TableReference
{

    private $class_name;

    public function __construct($entity_class)
    {
        $this->class_name = $entity_class;
    }

    public function asType()
    {
        return SchemaMapping::getTable($this->class_name)->getName();
    }
}
