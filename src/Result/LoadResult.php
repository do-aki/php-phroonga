<?php

namespace dooaki\Phroonga\Result;

use dooaki\Phroonga\GroongaResult;
use dooaki\Phroonga\Table;
use dooaki\Phroonga\Column;

class LoadResult extends GroongaResult {
    private $affected_count;

    public function each(callable $callback) {
        // Do Nothing
    }

    public function getAffectedCount() {
        return $this->affected_count;
    }

    public static function fromArray(array $result) {
        $r = parent::fromArray($result);
        $body = $r->getBody();

        $self = new self();
        $self->affected_count = $body;
        return $self;
    }
}