<?php
namespace Application\Service\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Cache\Storage\Adapter\RedisOptions;
use Zend\Cache\Storage\Adapter\Redis;

class RedisFactory implements FactoryInterface {

    public function createService(ServiceLocatorInterface $serviceLocator) {

        $config = $serviceLocator->get ( 'Config' );
        $config = $config ['redis'];

        $redisOptions = new RedisOptions ();
        $redisOptions->setServer ( array (
            'host' => $config ["host"],
            'port' => $config ["port"],
            'timeout' => '30'
        ) );

        $redisOptions->setLibOptions ( array (
            \Redis::OPT_SERIALIZER => \Redis::SERIALIZER_PHP
        ) );

        $redis = new Redis ( $redisOptions );

        return $redis;
    }
}