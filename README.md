php-phroonga
=============

[![Build Status](https://travis-ci.org/do-aki/php-phroonga.svg?branch=master)](https://travis-ci.org/do-aki/php-phroonga)
[![Coverage Status](https://img.shields.io/coveralls/do-aki/php-phroonga.svg)](https://coveralls.io/r/do-aki/php-phroonga?branch=master)

[Groonga](http://groonga.org/) client for php

namespace
-------------
dooaki\Phroonga

requirements
-------------
php 5.5.0 or latter

installation
-------------

write composer.json and run *composer install*.

```json:composer.json
{
   "require": {
      "dooaki/phroonga": "dev-master"
   }
}
```

usage
-------------

GroongaEntity trait を use したクラスが entity として、 groonga の一つのテーブルを司ります。

table 定義は _schema という static method で self::Table, self::Column 等を用いて行います。


```php
use dooaki\Phroonga\Groonga;
use dooaki\Phroonga\GroongaEntity;

class User
{
    use GroongaEntity;

    public static function _schema()
    {
        self::Table(
            'Users',
            [
                'flags' => 'TABLE_HASH_KEY',
                'key_type' => 'ShortText'
            ]
        );
        self::Column('age', 'Int32');
    }
}

$grn = new Groonga('http://localhost:10043');
$grn->activate();   // associate GroongaEntity and Groonga

$grn->create();  // table_create and column_create

$u1 = new User();
$u1->_key = 'alice';
$u1->age  = 18;
$u1->save();    // load

$u2 = new User();
$u2->_key = 'bob';
$u2->age  = 20;
$u2->save();    // load

$alice = User::select()->find('alice'); // select
echo "{$alice->_key} is {$alice->age} years old.", PHP_EOL;
```

上記のコードは groonga のコンソールで以下のコマンドを実行することと同じです

```
> table_create --name User --flags TABLE_HASH_KEY --key_type ShortText
> column_create --table User --name age --type Int32
> load --table User
[{"_key":"alice","age":18},{"_key":"bob","age":20}]
> select --table User --query "_key:alice"
```

