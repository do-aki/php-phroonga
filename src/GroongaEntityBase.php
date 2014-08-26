<?php

namespace dooaki\Phroonga;

use dooaki\Phroonga\Exception\InvalidType;
use dooaki\Phroonga\Exception\InvalidReferenceKey;

trait GroongaEntityBase {
    protected $grn_row = [];
    protected $grn_temporary = [];

    public function getReferenceKey() {
        $key_prop = $this->getDefinition()->hasKey() ? '_key' : '_id';
        $key = $this->__get($key_prop);
        if ($key === null) {
            throw new InvalidReferenceKey("no '{$key_prop}'");
        }
        return $key;
    }

    public function setArray(array $properties) {
        foreach ($properties as $name => $value) {
            $this->__set($name, $value);
        }

        return $this;
    }

    public function toArray() {
        return $this->grn_row + $this->grn_temporary;
    }

    public function toJsonForLoad() {
        $value_to_json = function ($value) {
            return is_object($value) ? $value->getReferenceKey() : $value;
        };
        $serialize = [];

        foreach ($this->grn_row as $name => $value) {
            if ($name === '_id') {
                continue;
            }

            if (is_array($value)) {
                $serialize[$name] = array_map($value_to_json, $value);
            } else {
                $serialize[$name] = $value_to_json($value);
            }
        }

        return json_encode($serialize);
    }

    public function __set($name, $value) {
        $validator = $this->getDefinition()->getColumn($name)->getValidator($name);
        if ($validator && !$validator->isValid($value)) {
            throw new InvalidType();
        }

        $this->grn_row[$name] = $value;
    }

    public function __get($name) {
        if (isset($this->grn_row[$name])) {
            return $this->grn_row[$name];
        }

        if (isset($this->grn_temporary[$name])) {
            return $this->grn_temporary[$name];
        }

        return null;
    }

    public function __isset($name) {
        return isset($this->grn_row[$name]) || isset($this->grn_temporary[$name]);
    }

    public function __unset($name) {
        unset($this->grn_row[$name]);
        unset($this->grn_temporary[$name]);
    }

    public static function restore(array $properties) {
        $cls = __CLASS__;
        $self = new $cls();
        $self->setArray($properties);
        return $self;
    }
}
