<?php
namespace dooaki\Test\Phroonga;

use dooaki\Phroonga\Result\HashResult;
use dooaki\Phroonga\GroongaResult;

class HashResultTest extends \PHPUnit_Framework_TestCase
{

    public function test_fromJson()
    {
        $result = HashResult::fromJson(<<<'_JSON'
[[0,1409105697.38424,6.15119934082031e-05],{"alloc_count":143,"starttime":1409105676,"uptime":21,"version":"4.0.4","n_queries":0,"cache_hit_rate":0.0,"command_version":1,"default_command_version":1,"max_command_version":2}]
_JSON
        );

        $this->assertSame(GroongaResult::GRN_SUCCESS, $result->getReturnCode());
        $this->assertSame(1409105697.3842399, $result->getCommandStartedTimestamp());
        $this->assertSame(6.1511993408203098E-5, $result->getElapsedSec());

        $this->assertSame(143, $result->alloc_count);
        $this->assertSame(1409105676, $result->starttime);
        $this->assertSame(21, $result->uptime);
        $this->assertSame("4.0.4", $result->version);
        $this->assertSame(0, $result->n_queries);
        $this->assertSame(0.0, $result->cache_hit_rate);
        $this->assertSame(1, $result->command_version);
        $this->assertSame(1, $result->default_command_version);
        $this->assertSame(2, $result->max_command_version);
    }
}

