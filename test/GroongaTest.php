<?php
namespace dooaki\Test\Phroonga;

use dooaki\Phroonga\Groonga;

class GroongaTest extends \PHPUnit_Framework_TestCase
{




    public function test_parseDsn()
    {
        $this->assertSame(
            [
              'protocol' =>  'http',
              'host'     =>  'example.com',
              'port'     =>  '',
              'path'     =>  '',
            ],
            Groonga::parseDsn('http://example.com')
        );
    }

    public function test_parseDsn_with_port()
    {
        $this->assertSame(
            [
              'protocol' =>  'gqtp',
              'host'     =>  'example.com',
              'port'     =>  '10041',
              'path'     =>  '',
            ],
            Groonga::parseDsn('gqtp://example.com:10041')
        );
    }

    public function test_parseDsn_with_path()
    {
        $this->assertSame(
            [
              'protocol' =>  'file',
              'host'     =>  '',
              'port'     =>  '',
              'path'     =>  '/tmp/grn.db',
            ],
            Groonga::parseDsn('file:///tmp/grn.db')
        );
    }
}

