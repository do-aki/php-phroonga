<?php

namespace dooaki\Phroonga\Result;

use dooaki\Phroonga\GroongaResult;
use dooaki\Phroonga\Table;
use dooaki\Phroonga\Column;

class BooleanResult extends GroongaResult {

    /**
     * @return boolean
     */
    public function getResult() {
        return $this->result;
    }

    public static function fromArray(array $result) {
        $r = parent::fromArray($result);

        $self = new self();
        $self->return = $r->getBody();
        return $self;
    }
}