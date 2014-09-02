<?php
namespace dooaki\Test\Phroonga\Model;

use dooaki\Phroonga\GroongaEntity;

class Synopsis
{
    use GroongaEntity;

    public static function _schema()
    {
        self::Table(
            'Synopsis',
            [
                'flags' => 'TABLE_HASH_KEY',
                'key_type' => 'ShortText'
            ]
        );
        self::Column('value', 'Int32');
    }
}
