<?php

namespace dooaki\Phroonga;

interface DriverInterface {

    /**
     * @param string $host
     * @param integer $port
     * @return void
     */
    public function connect($host, $port);

    /**
     * @return \dooaki\Phroonga\Result\HashResult
     */
    public function status();

    /**
     * @return \dooaki\Phroonga\Result\ListResult
     */
    public function tableList();

    /**
     * @param string $name
     * @param array $options
     * @return \dooaki\Phroonga\Result\BooleanResult
     */
    public function tableCreate($name, array $options);

    /**
     * @param string $name
     * @return \dooaki\Phroonga\Result\BooleanResult
     */
    public function tableRemove($name);

    /**
     * @param string $table
     * @return \dooaki\Phroonga\Result\ListResult
     */
    public function columnList($table);

    /**
     * @param string $table
     * @param string $name
     * @param string $flags
     * @param string $type
     * @param string $source
     * @return \dooaki\Phroonga\Result\BooleanResult
     */
    public function columnCreate($table, $name, $flags, $type, $source = null);

    /**
     * @param string $table
     * @param string $name
     * @return \dooaki\Phroonga\Result\BooleanResult
     */
    public function columnRemove($table, $name);

    /**
     * @param string $table
     * @param string $data
     * @return \dooaki\Phroonga\Result\LoadResult
     */
    public function load($table, $data);

    /**
     * @param string $table
     * @param array $params
     * @return \dooaki\Phroonga\Result\SelectResult
     */
    public function select($table, array $params);
}
