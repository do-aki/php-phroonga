<?php
namespace dooaki\Test\Phroonga\Model;

use dooaki\Phroonga\GroongaEntity;

class User
{
    use GroongaEntity;

    public static function _schema()
    {
        self::Table(
            'TestUser',
            [
                'flags' => 'TABLE_HASH_KEY',
                'key_type' => 'ShortText'
            ]
        );
        self::Column('name', 'ShortText');
        self::Column('age', 'Int32');
        self::Column('team', self::Reference(Team::class));
    }
}
