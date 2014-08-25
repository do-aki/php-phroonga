<?php

namespace dooaki\Phroonga;

use dooaki\Phroonga\Exception\InvalidEntity;
use dooaki\Phroonga\Exception\InvalidType;
use dooaki\Phroonga\Exception\InvalidReferenceKey;

trait GroongaEntity {
    protected $row = [];
    protected $temporary;
    protected static $grn;

    protected static function Column($name, $type, array $options = array()) {
        SchemaMapping::getTable(get_called_class())->addColumn(new Column($name, $type, $options));
    }

    protected static function Table($name, array $options = array()) {
        SchemaMapping::registerTable(get_called_class(), new Table($name, $options));
    }

    protected static function Reference($entity_class) {
        if (!in_array(GroongaEntity::class, class_uses($entity_class))) {
            throw new InvalidEntity("{$entity_class} is not GroongaEntity");
        }
        return new TableReference($entity_class);
    }

    public function getDefinition() {
        return SchemaMapping::getTable(__CLASS__);
    }

    public function values(array $values) {
        foreach ($values as $name => $value) {
            $this->$name = $value;
        }
        return $this;
    }

    public static function _activate(Groonga $grn) {
        self::$grn = $grn;
        $entity_name = static::_schema();
        // TODO: registerTable が呼ばれたかチェック
        return $entity_name;
    }

    public function getReferenceKey() {
        $key_prop = $this->getDefinition()->hasKey() ? '_key' : '_id';
        $key = $this->__get($key_prop);
        if ($key === null) {
            throw new InvalidReferenceKey("no '{$key_prop}'");
        }
        return $key;
    }

    public function toJsonForLoad() {
        $value_to_json = function ($value) {
            return is_object($value) ? $value->getReferenceKey() : $value;
        };
        $serialize = [];

        foreach ($this->row as $name => $value) {
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

        $this->row[$name] = $value;
    }

    public function __get($name) {
        if (isset($this->row[$name])) {
            return $this->row[$name];
        }

        if (isset($this->temporary[$name])) {
            return $this->temporary[$name];
        }

        return null;
    }

    public function __isset($name) {
        return isset($this->row[$name]) || isset($this->temporary[$name]);
    }

    public function __unset($name) {
        unset($this->row[$name]);
        unset($this->temporary[$name]);
    }

    public function save() {
        self::$grn->save($this);
    }

    public static function select() {
        // TODO: self::$grn が設定されてない場合は ativate されてない
        return self::$grn->select(__CLASS__);
    }

    public static function __set_state(array $properties) {
        $cls = __CLASS__;
        $self = new $cls();
        foreach ($properties as $name => $value) {
            $self->__set($name, $value);
        }
        return $self;
    }
}
