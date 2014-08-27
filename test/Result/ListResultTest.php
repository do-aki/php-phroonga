<?php
namespace dooaki\Test\Phroonga;

use dooaki\Phroonga\Result\ListResult;
use dooaki\Phroonga\GroongaResult;
use dooaki\Phroonga\Column;
use dooaki\Phroonga\ResultEntity;

class ListResultTest extends \PHPUnit_Framework_TestCase
{

    public function test_fromArray()
    {
        $result = ListResult::fromArray([
            [0,1409106873.81304,0.00175356864929199],
            [
                [
                    ["id","UInt32"],
                    ["name","ShortText"],
                    ["path","ShortText"],
                    ["flags","ShortText"],
                    ["domain","ShortText"],
                    ["range","ShortText"],
                    ["default_tokenizer","ShortText"],
                    ["normalizer","ShortText"]
                ],
                [256,"Tests","/groonga/db.0000100","TABLE_NO_KEY|PERSISTENT",null,null,null,null],
                [270,"Synopsis","/groonga/db.000010E","TABLE_HASH_KEY|PERSISTENT","ShortText",null,null,null]
            ]
        ]);

        $this->assertSame(GroongaResult::GRN_SUCCESS, $result->getReturnCode());
        $this->assertSame(1409106873.81304, $result->getCommandStartedTimestamp());
        $this->assertSame(0.00175356864929199, $result->getElapsedSec());

        $columns = $result->getColumnEnumerator()
            ->mapKeyValue(function ($idx, Column $c) { return [$c->getName() => $c->getType()]; })
            ->toArray();

        $this->assertSame(
            [
                'id' => 'UInt32',
                'name' => 'ShortText',
                'path' => 'ShortText',
                'flags' => 'ShortText',
                'domain' => 'ShortText',
                'range' => 'ShortText',
                'default_tokenizer' => 'ShortText',
                'normalizer' => 'ShortText',
            ],
            $columns
        );

        $tables = $result
            ->map(function (ResultEntity $r) { return $r->name; })
            ->toArray();
        $this->assertSame(
            ['Tests', 'Synopsis'],
            $tables
        );
    }
}

