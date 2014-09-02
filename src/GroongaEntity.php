<?php
namespace dooaki\Phroonga;

use dooaki\Phroonga\Exception\InvalidEntity;
use dooaki\Phroonga\Exception\InvalidType;
use dooaki\Phroonga\Exception\InvalidReferenceKey;

trait GroongaEntity
{
    use GroongaEntityBase;

    protected static $grn;
    protected static $grn_defining = false;

    public function getDefinition()
    {
        return SchemaMapping::getTable(__CLASS__);
    }

    public function save()
    {
        self::$grn->save($this);
    }

    public function toJsonForLoad()
    {
        $value_to_json = function ($value) {
            return is_object($value) ? $value->getReferenceKey() : $value;
        };

        $serialize = ['_key' => $this->grn_values['_key']];

        //TODO: _key が変更されていたら警告する?
        foreach (array_keys($this->grn_changed) as $name) {
            if ($name === '_id' || $name === '_key') {
                continue;
            }

            $column = $this->getDefinition()->tryGetColumn($name);
            if (!$column) {
                continue;
            }

            $value = $this->grn_values[$name];
            if (is_array($value)) {
                $serialize[$name] = array_map($value_to_json, $value);
            } else {
                $serialize[$name] = $value_to_json($value);
            }
        }

        return json_encode($serialize);
    }

    protected static function Column($name, $type, array $options = array())
    {
        if (!self::$grn_defining) {
            return;
        }

        SchemaMapping::getTable(get_called_class())->addColumn(new Column($name, $type, $options));
    }

    protected static function Table($name, array $options = array())
    {
        if (!self::$grn_defining) {
            return;
        }

        $table = new Table($name, $options);
        $table->addColumn(
            new Column('_id', 'Uint32', [
                'flags' => 'COLUMN_SCALAR'
            ])
        );

        SchemaMapping::registerTable(get_called_class(), $table);
    }

    protected static function Reference($entity_class)
    {
        if (!in_array(GroongaEntity::class, class_uses($entity_class))) {
            throw new InvalidEntity("{$entity_class} is not GroongaEntity");
        }
        return new TableReference($entity_class);
    }

    public static function _activate(Groonga $grn)
    {
        self::$grn = $grn;
        self::$grn_defining = true;
        $entity_name = static::_schema();
        self::$grn_defining = false;

        // TODO: registerTable が呼ばれたかチェック
        return $entity_name;
    }

    public static function select()
    {
        // TODO: self::$grn が設定されてない場合は ativate されてない
        return self::$grn->select(__CLASS__);
    }
    
}
