<?php

namespace Spear\Silex\Provider;

use Silex\ServiceProviderInterface;
use Puzzle\Configuration;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;

class DBAL implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $this->validatePuzzleConfiguration($app);

        $app->register(new DoctrineServiceProvider(), array(
            'db.options' => array(
                'driver'   => 'pdo_mysql',
                'dbname'   => $app['configuration']->readRequired('db/database'),
                'host'     => $app['configuration']->readRequired('db/host'),
                'user'     => $app['configuration']->readRequired('db/user'),
                'password' => $app['configuration']->readRequired('db/password'),
                'port'     => $app['configuration']->read('db/port', 3306),
                'charset'  => $app['configuration']->read('db/charset', 'utf8'),
            )
        ));
    }

    public function boot(Application $app)
    {
        ;
    }

    private function validatePuzzleConfiguration(Application $app)
    {
        if(! isset($app['configuration']) || ! $app['configuration'] instanceof Configuration)
        {
            throw new \LogicException(__CLASS__ . ' requires an instance of puzzle/configuration (as key "configuration")');
        }
    }
}
