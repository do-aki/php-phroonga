<?php
namespace dooaki\Test\Phroonga\Factory;

use dooaki\Phroonga\Groonga;
use dooaki\Phroonga\Factory\Factory;
use dooaki\Test\Phroonga\Model\User;
use dooaki\Test\Phroonga\Model\Team;
use dooaki\Phroonga\Driver\Mock;
use dooaki\Phroonga\Result\LoadResult;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public function factory($groonga) {
        return new Factory(
            function () {
                Factory::define(Team::class, [
                    '_key' => Factory::sequence(function ($n) { return "team_{$n}"; }),
                ]);
                Factory::define(['team_red' => Team::class], ['rgb' => '#ff0000']);
                Factory::define(['team_green' => Team::class], ['rgb' => '#00ff00']);
                Factory::define(User::class, [
                    '_key' => Factory::sequence(function ($n) { return "name_{$n}"; }),
                    'name' => '名無し',
                ]);
            },
            $groonga
        );
    }

    public function test_build()
    {
        $f = $this->factory(new Groonga('mock://'));

        $u = Factory::build(User::class, ['team' => 'team_red']);

        $this->assertSame('名無し', $u->name);
        $this->assertInstanceOf(Team::class, $u->team);
        $this->assertSame('team_red', $u->team->_key);
    }

    /**
     * @expectedException \dooaki\Phroonga\Exception\PhroongaException
     */
    public function test_dispose()
    {
        $f = $this->factory(new Groonga('mock://'));
        $f->dispose();

        Factory::build(User::class);
    }
}
