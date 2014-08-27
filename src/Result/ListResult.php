<?php
namespace dooaki\Phroonga\Result;

use dooaki\Phroonga\GroongaResult;
use dooaki\Phroonga\ResultEntity;
use dooaki\Phroonga\Column;
use dooaki\Container\Lazy\Enumerable;
use dooaki\Container\Lazy\Enumerator;

class ListResult extends GroongaResult
{
    use Enumerable;

    private $columns = [];

    private $rows = [];

    public function getColumns()
    {
        foreach ($this->columns as $column) {
            yield new Column($column[0], $column[1], []);
        }
    }

    public function getRows()
    {
        $columns = $this->getColumnEnumerator()->toArray();
        $column_names = $this->getColumnEnumerator()->map(
            function (Column $c) {
                return $c->getName();
            }
        )->toArray();

        foreach ($this->rows as $row) {
            yield (new ResultEntity($columns))->setArray(array_combine($column_names, $row));
        }
    }

    public function getColumnEnumerator()
    {
        return new Enumerator([$this, 'getColumns']);
    }

    public function each(callable $callback = null)
    {
        if ($callback === null) {
            return $this->getRows();
        } else {
            $this->apply($callback);
        }
    }

    /**
     *
     * @param array $result
     * @return \dooaki\Phroonga\Result\ListResult
     */
    public static function fromArray(array $result)
    {
        $self = parent::fromArray($result);
        $body = $self->getBody();

        $self->columns = array_shift($body);
        $self->rows = $body;

        return $self;
    }
}
