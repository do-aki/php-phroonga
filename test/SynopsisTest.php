<?php
namespace dooaki\Test\Phroonga;

use dooaki\Phroonga\Groonga;
use dooaki\Phroonga\GroongaEntity;
use dooaki\Phroonga\Table;

class SynopsisTest_Entity
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

class SynopsisTest extends \PHPUnit_Framework_TestCase
{

    private $grn;

    public function setUp()
    {
        $host = getenv('GROONGA_TEST_HOST');
        $port = getenv('GROONGA_TEST_PORT') ?  : 10043;

        if (empty($host)) {
            $this->markTestSkipped('no GROONGA_TEST_HOST');
        }

        $this->grn = new Groonga($host, $port);
        $this->grn->activate(
            [
                SynopsisTest_Entity::class
            ]
        );

        $this->grn->tables()->each(function (Table $t)
        {
            if ($t->getName() == 'Synopsis') {
                $t->removeColumns($this->grn->getDriver());
                $t->removeTable($this->grn->getDriver());
            }
        });
        $this->grn->create();
    }

    public function test_status()
    {
        $status = $this->grn->status();
        $this->assertTrue($status->uptime > 0);
    }

    public function test_save_and_select()
    {
        $e = new SynopsisTest_Entity();
        $e->_key = "key";
        $e->value = 999;
        $e->save();

        $r = SynopsisTest_Entity::select()->query('_key:?', 'key')->findFirst();
        $this->assertSame("key", $r->_key);
        $this->assertSame(999, $r->value);
    }

    public function test_fail()
    {
        SynopsisTest_Entity::select()->query('invalid_column:?')->findFirst();
    }
}
