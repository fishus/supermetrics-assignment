<?php

namespace SocialPost\Cache\Factory;

use Memcached;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;
use Symfony\Component\Cache\Psr16Cache;

/**
 * Class CacheFactory
 *
 * @package SocialPost\Cache\Factory
 */
class CacheFactory
{

    /**
     * @throws \Exception
     * @return CacheInterface
     */
    public static function create(): CacheInterface
    {
        $psr6Cache = new MemcachedAdapter(self::getClient());
        $psr16Cache = new Psr16Cache($psr6Cache);
        return $psr16Cache;
    }

    /**
     * @return Memcached
     */
    protected static function getClient(): Memcached
    {
        return MemcachedAdapter::createConnection($_ENV['MEMCACHED_DSN']);
    }
}
