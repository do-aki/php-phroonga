<?php
namespace dooaki\Phroonga\Driver;

use Guzzle\Http\Client;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Http\Exception\BadResponseException;
use dooaki\Phroonga\DriverInterface;
use dooaki\Phroonga\Result\ListResult;
use dooaki\Phroonga\Result\SelectResult;
use dooaki\Phroonga\Result\LoadResult;
use dooaki\Phroonga\Result\BooleanResult;
use dooaki\Phroonga\Result\HashResult;

class Http implements DriverInterface
{
    /**
     * @var Guzzle\Http\Client
     */
    private $client;

    public function connect($host, $port)
    {
        $this->client = new Client("http://{$host}:{$port}");
    }

    public function status()
    {
        $request = $this->client->get('/d/status.json');
        $response = $this->_sendRequest($request);

        return HashResult::fromJson((string)$response->getBody());
    }

    public function tableList()
    {
        $request = $this->client->get('/d/table_list.json');
        $response = $this->_sendRequest($request);

        return ListResult::fromJson((string)$response->getBody());
    }

    public function tableCreate($name, array $options)
    {
        $request = $this->client->get('/d/table_create.json');
        $query = $request->getQuery();
        $query->set('name', $name);
        foreach ($options as $k => $v) {
            $query->set($k, $v);
        }
        $response = $this->_sendRequest($request);

        return BooleanResult::fromJson((string)$response->getBody());
    }

    public function tableRemove($name)
    {
        $request = $this->client->get('/d/table_remove.json');
        $query = $request->getQuery();
        $query->set('name', $name);
        $response = $this->_sendRequest($request);

        return BooleanResult::fromJson((string)$response->getBody());
    }

    public function columnList($table)
    {
        $request = $this->client->get('/d/column_list.json');
        $query = $request->getQuery();
        $query->set('table', $table);
        $response = $this->_sendRequest($request);

        return ListResult::fromJson((string)$response->getBody());
    }

    public function columnCreate($table, $name, $flags, $type, $source = null)
    {
        $request = $this->client->get('/d/column_create.json');
        $query = $request->getQuery();
        $query->set('table', $table);
        $query->set('name', $name);
        $query->set('flags', $flags);
        $query->set('type', $type);
        if ($source !== null) {
            $query->set('source', $source);
        }
        $response = $this->_sendRequest($request);

        return BooleanResult::fromJson((string)$response->getBody());
    }

    public function columnRemove($table, $name)
    {
        $request = $this->client->get('/d/column_remove.json');
        $query = $request->getQuery();
        $query->set('table', $table);
        $query->set('name', $name);
        $response = $this->_sendRequest($request);

        return BooleanResult::fromJson((string)$response->getBody());
    }

    public function load($table, $data)
    {
        $request = $this->client->get('/d/load.json');
        $query = $request->getQuery();
        $query->set('table', $table);
        $query->set('values', $data);
        $response = $this->_sendRequest($request);

        return LoadResult::fromJson((string)$response->getBody());
    }

    public function select($table, array $params)
    {
        $request = $this->client->get('/d/select.json');
        $query = $request->getQuery();
        $query->set('table', $table);
        foreach ($params as $k => $v) {
            $query->set($k, $v);
        }
        $response = $this->_sendRequest($request);

        return SelectResult::fromJson((string)$response->getBody());
    }

    private function _sendRequest(RequestInterface $request)
    {
        try {
            return $request->send();
        } catch (ClientErrorResponseException $e) {
            return $e->getResponse();
        }
    }
}
