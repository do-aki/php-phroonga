<?php

namespace dooaki\Phroonga;

interface DriverInterface {

    public function connect($host, $port);

    public function tableList();

    public function tableCreate($name, array $options);

    public function columnCreate($table, $name, $flags, $type, $source = null);

    public function load($table, $data);

    public function select($table, array $params);
}

