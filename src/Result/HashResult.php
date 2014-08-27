<?php
namespace dooaki\Phroonga\Result;

use dooaki\Phroonga\GroongaResult;
use dooaki\Phroonga\Table;
use dooaki\Phroonga\Column;

class HashResult extends GroongaResult implements \IteratorAggregate
{
    private $properies = [];

    public function __get($name)
    {
        if (isset($this->properies[$name])) {
            return $this->properies[$name];
        }
        return null;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->properies);
    }

    public static function fromArray(array $result)
    {
        $self = parent::fromArray($result);
        $self->properies = $self->getBody();
        return $self;
    }
}
