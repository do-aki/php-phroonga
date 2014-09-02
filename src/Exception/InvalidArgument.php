<?php
namespace dooaki\Phroonga\Exception;

class InvalidArgument extends PhroongaException
{
    private $arguments;

    public function setArguments(array $args) {
        $this->arguments = $args;
    }

    public function getArguments() {
        return $this->arguments;
    }
}