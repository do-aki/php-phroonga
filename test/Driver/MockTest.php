<?php
namespace dooaki\Test\Phroonga\Factory;

use dooaki\Phroonga\Groonga;
use dooaki\Phroonga\Driver\Mock;
use dooaki\Phroonga\Result\HashResult;
use dooaki\Phroonga\Result\ListResult;
use dooaki\Phroonga\GroongaResult;
use dooaki\Phroonga\Result\BooleanResult;
use dooaki\Phroonga\Result\SelectResult;

class MockTest extends \PHPUnit_Framework_TestCase
{
    public function test_status_array()
    {
        $mock = new Mock();
        $mock->pushExpects(
            'status',
            [
                [GroongaResult::GRN_SUCCESS,1,2],
                ["alloc_count" => 143,"starttime" => 1409105676,"uptime" => 21]
            ]
        );

        $r = $mock->status();
        $this->assertInstanceOf(HashResult::class, $r);
        $this->assertSame(GroongaResult::GRN_SUCCESS, $r->getReturnCode());
        $this->assertSame(21, $r->uptime);
    }

    public function test_tableList_json()
    {
        $mock = new Mock();
        $mock->pushExpects(
            'tableList',
            json_encode([
                [GroongaResult::GRN_SUCCESS,1,2],
                [
                    [
                        ["id","UInt32"],
                        ["name","ShortText"],
                    ],
                    [1,"hoge"],
                    [2,"hage"],
                ]
            ])
        );

        $r = $mock->tableList();
        $this->assertInstanceOf(ListResult::class, $r);
        $this->assertSame(GroongaResult::GRN_SUCCESS, $r->getReturnCode());

        $table = $r->toArray();
        $this->assertSame(1, $table[0]->id);
        $this->assertSame('hoge', $table[0]->name);
        $this->assertSame(2, $table[1]->id);
        $this->assertSame('hage', $table[1]->name);
    }

    public function test_tableCreate_object()
    {
        $mock = new Mock();
        $result = new BooleanResult();
        $result->setReturnCode(GroongaResult::GRN_SYNTAX_ERROR);
        $mock->pushExpects('tableCreate', $result);

        $r = $mock->tableCreate('name', []);
        $this->assertInstanceOf(BooleanResult::class, $r);
        $this->assertSame(GroongaResult::GRN_SYNTAX_ERROR, $r->getReturnCode());
    }

    /**
     * @expectedException \dooaki\Phroonga\Exception\DriverError
     */
    public function test_columnCreate_input_args()
    {
        $mock = new Mock();
        $mock->pushExpects('columnCreate', new BooleanResult(), ['tbl', 'name', 'falgs', 'type']);

        $r = $mock->columnCreate('tbl', 'name', 'falgs', 'type', ['orverargs']);
        $this->assertInstanceOf(BooleanResult::class, $r);
    }

    public function test_select_input_callable()
    {
        $mock = new Mock();
        $called = false;
        $mock->pushExpects('select', new SelectResult(), function ($table, $options) use(&$called) {
            $this->assertSame('tbl', $table);
            $this->assertSame(['query' => 'xxx'], $options);
            $called = true;
        });

        $r = $mock->select('tbl', ['query' => 'xxx']);
        $this->assertInstanceOf(SelectResult::class, $r);

        $this->assertTrue($called);
    }

    /**
     * @expectedException \dooaki\Phroonga\Exception\DriverError
     */
    public function test_no_state()
    {
        $mock = new Mock();
        $mock->status();
    }
}