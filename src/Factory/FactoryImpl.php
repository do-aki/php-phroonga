<?php
namespace dooaki\Phroonga\Factory;

use dooaki\Phroonga\SchemaMapping;
use dooaki\Phroonga\Groonga;

class FactoryImpl
{
    private $entity_class;

    private $base;

    private $props;

    public function __construct($entity_class, $base, $props)
    {
        $this->entity_class = $entity_class;
        $this->base = $base;
        $this->props = $props;
    }

    public function getEntityClass()
    {
        return $this->entity_class;
    }

    public function build(array $props)
    {
        $cls = $this->entity_class;

        $table = SchemaMapping::getTable($cls);

        foreach ($props as $k => $v) {
            $column = $table->tryGetColumn($k);
            if ($column && $column->typeIsReference()) {
                if (Factory::isDefined($v)) {
                    $props[$k] = Factory::build($v, ['_key' => $v]);
                }
            }
        }

        return $cls::restore($props + $this->buildBase());
    }

    private function buildBase()
    {
        $props = $this->base ? $this->base->buildBase() : [];

        foreach ($this->props as $k => $v) {
            if (is_callable($v)) {
                $props[$k] = call_user_func($v);
            } else {
                $props[$k] = $v;
            }
        }

        return $props;
    }
}

