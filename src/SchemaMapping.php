<?php
namespace dooaki\Phroonga;

use dooaki\Phroonga\Exception\TableNotfound;

class SchemaMapping
{

    static $table = [];

    public static function registerTable($cls, Table $td)
    {
        self::$table[$cls] = $td;
    }

    /**
     *
     * @param string $cls
     *            class name
     * @throws TableNotfound
     * @return Table
     */
    public static function getTable($cls)
    {
        if (!isset(self::$table[$cls])) {
            throw new TableNotfound("class '{$cls}' is not registered in SchemaMapping");
        }
        return self::$table[$cls];
    }
}
