<?php

namespace dooaki\Phroonga;

class ResultEntity {

    use GroongaEntityBase;

    protected $table;

    public function __construct(array $columns) {
        $this->table = new Table('_result', []);
        foreach ($columns as $c) {
            $this->table->addColumn($c);
        }
    }

    public function getDefinition() {
        return $this->table;
    }

}
