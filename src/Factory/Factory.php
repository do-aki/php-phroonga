<?php
namespace dooaki\Phroonga\Factory;

use dooaki\Phroonga\Groonga;
use dooaki\Phroonga\Exception\PhroongaException;
use Hoge\User;

class Factory
{
    private static $current;

    private $factory;
    private $grn;
    private $entity_classes;

    public function __construct($file_or_callable, Groonga $grn = null)
    {
        self::$current = $this;
        $this->grn = $grn;

        if (is_callable($file_or_callable)) {
            call_user_func($file_or_callable);
        } else {
            require $file;
        }

        if ($grn !== null) {
            $grn->activate(array_keys($this->entity_classes));
        }
    }

    public static function define($definition, $props)
    {
        if (is_array($definition)) {
            $access_name = key($definition);
            $base_name = current($definition);
        } else {
            $access_name = $definition;
            $base_name = $definition;
        }

        if (isset(self::$current->factory[$base_name])) {
            $base = self::$current->factory[$base_name];
            $entity_class = $base->getEntityClass();
        } else {
            $base = null;
            $entity_class = $base_name;
        }

        self::$current->entity_classes[$entity_class] = true;
        self::$current->factory[$access_name] = new FactoryImpl($entity_class, $base, $props);
    }

    public static function sequence(callable $callback, $init = 1)
    {
        return function () use($callback, &$init) {
            return call_user_func($callback, $init++);
        };
    }

    public static function isDefined($access_name)
    {
        if (!self::$current) {
            throw new PhroongaException('no factory object');
        }
        return isset(self::$current->factory[$access_name]);
    }

    public static function build($access_name, array $props = [])
    {
        if (!self::$current) {
            throw new PhroongaException('no factory object');
        }
        return self::$current->factory[$access_name]->build($props);
    }

    public function dispose()
    {
        if (self::$current === $this) {
            self::$current = null;
        }
    }
}
