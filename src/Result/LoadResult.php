<?php

namespace dooaki\Phroonga\Result;

use dooaki\Phroonga\GroongaResult;
use dooaki\Phroonga\Table;
use dooaki\Phroonga\Column;

class LoadResult extends GroongaResult {
    private $affected_count;

    public function getAffectedCount() {
        return $this->affected_count;
    }

    public static function fromArray(array $result) {
        $self = parent::fromArray($result);
        $self->affected_count = $self->getBody();
        return $self;
    }
}