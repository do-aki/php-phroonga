<?php
namespace dooaki\Phroonga\Exception;

use dooaki\Phroonga\GroongaResult;

class CommandFailure extends PhroongaException
{
    private $result;

    public function setResult(GroongaResult $result)
    {
        $this->result = $result;
    }

    public function getResult() {
        $this->result;
    }
}