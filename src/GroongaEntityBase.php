<?php
namespace dooaki\Phroonga;

use dooaki\Phroonga\Exception\InvalidType;
use dooaki\Phroonga\Exception\InvalidReferenceKey;
use dooaki\Container\Lazy\Enumerator;

trait GroongaEntityBase
{
    protected $grn_values = [];

    protected $grn_changed = [];

    /**
     * @return \dooaki\Phroonga\Table
     */
    abstract function getDefinition();

    public function getReferenceKey()
    {
        $key_prop = $this->getDefinition()->getKeyName();
        $key = $this->__get($key_prop);
        if ($key === null) {
            throw new InvalidReferenceKey("no '{$key_prop}'");
        }
        return $key;
    }

    public function setArray(array $properties)
    {
        foreach ($properties as $name => $value) {
            $this->__set($name, $value);
        }

        return $this;
    }

    public function toArray()
    {
        return $this->grn_values;
    }

    public function __set($name, $value)
    {
        if (array_key_exists($name, $this->grn_values)) {
            if (!array_key_exists($name, $this->grn_changed)) {
                $this->grn_changed[$name] = $this->grn_values[$name];
            }
        } else {
            $this->grn_changed[$name] = $value;
        }
        $this->grn_values[$name] = $value;
    }

    public function __get($name)
    {
        if (isset($this->grn_values[$name])) {
            return $this->grn_values[$name];
        }

        return null;
    }

    public function __isset($name)
    {
        return isset($this->grn_values[$name]);
    }

    public function __unset($name)
    {
        unset($this->grn_values[$name]);
        unset($this->grn_changed[$name]);
    }

    public static function restore(array $properties)
    {
        $cls = __CLASS__;
        $self = new $cls();

        foreach ($properties as $name => $value) {
            $pos = strpos($name, '.');
            if ($pos === false) {
                if (isset($self->grn_values[$name])) {
                    $self->grn_values[$name]->_key = $value; // XXX: _id の場合
                } else {
                    $self->grn_values[$name] = $value;
                }
            } else {
                $col_name = substr($name, 0, $pos);
                $sub_name = substr($name, $pos+1);

                if (!isset($self->grn_values[$col_name])) {
                    $self->grn_values[$col_name] = new \StdClass();
                }
                $self->grn_values[$col_name]->$sub_name = $value;
            }
        }
        return $self;
    }
}
