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
    private $expect = [];

    private $return_class_mapping;

    public function __construct() {
        $this->return_class_mapping = [
            'connect'     => null,
            'status'      => HashResult::class,
            'tableList'   => ListResult::class,
            'tableCreate' => BooleanResult::class,
            'tableRemove' => BooleanResult::class,
            'columnList'  => ListResult::class,
            'columnCreate'=> BooleanResult::class,
            'columnRemove'=> BooleanResult::class,
            'load'        => LoadResult::class,
            'select'      => SelectResult::class,
        ];
    }

    public function setOptions(array $options)
    {
    }

    public function connect()
    {
    }

    public function status()
    {
        return $this->judge(__FUNCTION__, func_get_args());
    }

    public function tableList()
    {
        return $this->judge(__FUNCTION__, func_get_args());
    }

    public function tableCreate($name, array $options)
    {
        return $this->judge(__FUNCTION__, func_get_args());
    }

    public function tableRemove($name)
    {
        return $this->judge(__FUNCTION__, func_get_args());
    }

    public function columnList($table)
    {
        return $this->judge(__FUNCTION__, func_get_args());
    }

    public function columnCreate($table, $name, $flags, $type, $source = null)
    {
        return $this->judge(__FUNCTION__, func_get_args());
    }

    public function columnRemove($table, $name)
    {
        return $this->judge(__FUNCTION__, func_get_args());
    }

    public function load($table, $data)
    {
        return $this->judge(__FUNCTION__, func_get_args());
    }

    public function select($table, array $params)
    {
        return $this->judge(__FUNCTION__, func_get_args());
    }

    public function pushExpects($method, $output, $input = null)
    {
        $this->expect[] = [
            'method' => $method,
            'output' => $output,
            'input'  => $input,
        ];
    }

    public function judge($actual_method, $args)
    {
        if (!$this->expect) {
            throw new DriverError("expects underflow");
        }

        $expect = array_shift($this->expect);
        if ($actual_method !== $expect['method']) {
            throw new DriverError("method mismatch expect:{$expect['method']}, actual:{$actual_method}");
        }

        if ($expect['input']) {
            $this->checkValidInput($expect['input'], $args);
        }

        if (isset($this->return_class_mapping[$actual_method])) {
            return $this->makeResult($this->return_class_mapping[$actual_method], $expect['output']);
        } elseif ($expect['output']) {
            throw new DriverError("output expected but {$actual_method} methods return is nothing");
        }
    }

    public function checkValidInput($expect, $actual) {
        if (is_callable($expect)) {
            call_user_func_array($expect, $actual);
        } else {
            if ($expect !== $actual) {
                throw new DriverError(sprintf(
                    "mismatch input values \nexpect:%s\nactual:%s\n",
                    print_r($expect, true),
                    print_r($actual, true)
                ));
            }
        }
    }

    public function clearExpects()
    {
        $this->expect = [];
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
        throw new DriverError('unexpected expects data');
    }

}
