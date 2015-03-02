<?php

namespace Spear\Silex\Provider;

use Silex\ServiceProviderInterface;
use Puzzle\Configuration;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Puzzle\PrefixedConfiguration;

class DBAL implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $this->validatePuzzleConfiguration($app);
        $this->registerDatabases($app);
    }

    private function registerDatabases(Application $app)
    {
        $options = array();
        $configuration = $app['configuration'];

        $databases = array_keys($configuration->readRequired('db'));
        foreach($databases as $database)
        {
            $options[$database] = $this->registerDatabase($app, $configuration, $database);
        }

        $app->register(new DoctrineServiceProvider(), array(
            'dbs.options' => $options
        ));
    }

    private function registerDatabase(Application $app, Configuration $configuration, $database)
    {
        $configuration = new PrefixedConfiguration($configuration, "db/$database");

        $options = array(
            'driver'   => 'pdo_mysql',
            'dbname'   => $configuration->readRequired('database'),
            'host'     => $configuration->readRequired('host'),
            'user'     => $configuration->readRequired('user'),
            'password' => $configuration->readRequired('password'),
            'port'     => $configuration->read('port', 3306),
            'charset'  => $configuration->read('charset', 'utf8'),
        );

        // Declare helper
        $app["db.$database"] = $app->share(function () use ($app, $database) {
            return $app['dbs'][$database];
        });

        return $options;
    }

    public function boot(Application $app)
    {
    }

    private function validatePuzzleConfiguration(Application $app)
    {
        if(! isset($app['configuration']) || ! $app['configuration'] instanceof Configuration)
        {
            throw new \LogicException(__CLASS__ . ' requires an instance of puzzle/configuration (as key "configuration")');
        }
    }
}
