<?php
namespace dooaki\Test\Phroonga;

use dooaki\Phroonga\GroongaResult;
use dooaki\Phroonga\Result\BooleanResult;

class BooleanResultTest extends \PHPUnit_Framework_TestCase
{

    public function test_fromArray()
    {
        $result = BooleanResult::fromArray([
            [0,1409121816.29123,0.038550853729248],
            true
        ]);

        $this->assertSame(GroongaResult::GRN_SUCCESS, $result->getReturnCode());
        $this->assertSame(1409121816.29123, $result->getCommandStartedTimestamp());
        $this->assertSame(0.038550853729248, $result->getElapsedSec());

        $this->assertTrue($result->getResult());
    }
}
