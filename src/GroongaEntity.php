<?php

namespace dooaki\Phroonga;

use dooaki\Phroonga\Exception\InvalidEntity;
use dooaki\Phroonga\Exception\InvalidType;
use dooaki\Phroonga\Exception\InvalidReferenceKey;

trait GroongaEntity {
    use GroongaEntityBase;

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

    public static function _activate(Groonga $grn) {
        self::$grn = $grn;
        $entity_name = static::_schema();
        // TODO: registerTable が呼ばれたかチェック
        return $entity_name;
    }

    public function save() {
        self::$grn->save($this);
    }

    public static function select() {
        // TODO: self::$grn が設定されてない場合は ativate されてない
        return self::$grn->select(__CLASS__);
    }

}
