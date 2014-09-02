<?php
namespace dooaki\Test\Phroonga\Model;

use dooaki\Phroonga\GroongaEntity;

class Team
{
    use GroongaEntity;

    public static function _schema()
    {
        self::Table(
            'TestTeam',
            [
                'flags' => 'TABLE_PAT_KEY',
                'key_type' => 'ShortText'
            ]
        );
        self::Column('rgb', 'ShortText');
    }
}
