<?php

namespace dooaki\Phroonga\Driver;

use Groonga\Http\Client;
use dooaki\Phroonga\DriverInterface;
use dooaki\Phroonga\Result\TableListResult;
use dooaki\Phroonga\Result\SelectResult;
use dooaki\Phroonga\Result\LoadResult;

class Http implements DriverInterface {

    /**
     *
     * @var \Groonga\Http\Client
     */
    private $client;

    public function connect($host, $port) {
        $this->client = new Client("http://{$host}:{$port}");
    }

    public function tableList() {
        return TableListResult::fromArray($this->client->tableList());
    }

    public function tableCreate($name, array $options) {
        return $this->client->tableCreate($name, $options);
    }

    public function columnCreate($table, $name, $flags, $type, $source = null) {
        return $this->client->columnCreate($table, $name, $flags, $type, $source);
    }

    public function load($table, $data) {
        return LoadResult::fromArray($this->client->load($table, $data));
    }

    public function select($table, array $params) {
        return SelectResult::fromArray($this->client->select($table, $params));
    }
}
