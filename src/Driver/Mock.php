<?php
namespace dooaki\Phroonga\Driver;

use dooaki\Phroonga\DriverInterface;
use dooaki\Phroonga\Result\ListResult;
use dooaki\Phroonga\Result\SelectResult;
use dooaki\Phroonga\Result\LoadResult;
use dooaki\Phroonga\Result\BooleanResult;
use dooaki\Phroonga\Result\HashResult;
use dooaki\Phroonga\Exception\DriverError;

class Mock implements DriverInterface
{
    private $state = [];

    public function setOptions(array $options)
    {
    }

    public function connect()
    {
    }

    public function status()
    {
        return $this->makeResult(HashResult::class, $this->shiftState(__FUNCTION__));
    }

    public function tableList()
    {
        return $this->makeResult(ListResult::class, $this->shiftState(__FUNCTION__));
    }

    public function tableCreate($name, array $options)
    {
        return $this->makeResult(BooleanResult::class, $this->shiftState(__FUNCTION__));
    }

    public function tableRemove($name)
    {
        return $this->makeResult(BooleanResult::class, $this->shiftState(__FUNCTION__));
    }

    public function columnList($table)
    {
        return $this->makeResult(ListResult::class, $this->shiftState(__FUNCTION__));
    }

    public function columnCreate($table, $name, $flags, $type, $source = null)
    {
        return $this->makeResult(BooleanResult::class, $this->shiftState(__FUNCTION__));
    }

    public function columnRemove($table, $name)
    {
        return $this->makeResult(BooleanResult::class, $this->shiftState(__FUNCTION__));
    }

    public function load($table, $data)
    {
        return $this->makeResult(LoadResult::class, $this->shiftState(__FUNCTION__));
    }

    public function select($table, array $params)
    {
        return $this->makeResult(SelectResult::class, $this->shiftState(__FUNCTION__));
    }

    public function pushState($method, $data)
    {
        $this->state[$method][] = $data;
    }

    public function shiftState($method)
    {
        if (!isset($this->state[$method]) || !$this->state[$method]) {
            throw new DriverError("undefined state for method '{$method}' ");
        }

        return array_shift($this->state[$method]);
    }

    public function clearState($method = null)
    {
        if ($method === null) {
            $this->state = [];
        } else {
            unset($this->state[$method]);
        }
    }

    public function makeResult($cls, $data)
    {
        if ($data instanceof $cls) {
            return $data;
        } elseif (is_array($data)) {
            return $cls::fromArray($data);
        } elseif (is_string($data)) {
            return $cls::fromJson($data);
        }
        throw new DriverError('unexpected state data');
    }

}
