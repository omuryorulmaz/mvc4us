<?php
namespace Mvc4us\Cache;

/**
 *
 * @author erdem
 *
 */
class RedisCacheAdapter implements CacheInterface
{

    /**
     *
     * @var \Redis
     */
    private $redis;

    private $lastError;

    public function __construct(string $host, int $port = 6379, ?string $auth = null, int $db = 0)
    {
        $this->redis = new \Redis();

        if (! $this->redis->connect($host, $port)) {
            $this->lastError = sprintf('Connection failed to redis %s:%s', $host, $port);
            return;
        }

        $this->redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);

        if (! empty($auth)) {
            if (! $this->redis->auth($auth)) {
                $this->lastError = sprintf('Auth failed to redis %s:%s', $host, $port);
                return;
            }
        }

        $this->redis->select($db);
    }

    /**
     *
     * @see \Mvc4us\Cache\CacheInterface::get()
     */
    public function get($key, &$var): bool
    {

        // TODO - Insert your code here
        $var = $this->redis->get($key);
        return true;
    }

    /**
     *
     * @see \Mvc4us\Cache\CacheInterface::set()
     */
    public function set($key, $value, $ttl = null): bool
    {

        // TODO - Insert your code here
        $this->redis->set($key, $value);
        $this->redis->expire($key, $ttl);
        return true;
    }

    /**
     *
     * @see \Mvc4us\Cache\CacheInterface::delete()
     */
    public function delete($key): bool
    {

        // TODO - Insert your code here
    }

    /**
     *
     * @see \Mvc4us\Cache\CacheInterface::getItem()
     */
    public function getItem($key, $memberKey, &$var): bool
    {

        // TODO - Insert your code here
    }

    /**
     *
     * @see \Mvc4us\Cache\CacheInterface::setItem()
     */
    public function setItem($key, $memberKey, $value, $expiration = null): bool
    {

        // TODO - Insert your code here
    }

    /**
     *
     * @see \Mvc4us\Cache\CacheInterface::deleteItem()
     */
    public function deleteItem($key, $memberKey): bool
    {

        // TODO - Insert your code here
    }

    /**
     *
     * @see \Mvc4us\Cache\CacheInterface::clear()
     */
    public function clear(): bool
    {

        // TODO - Insert your code here
    }

    /**
     *
     * @see \Mvc4us\Cache\CacheInterface::has()
     */
    public function has($key): bool
    {

        // TODO - Insert your code here
    }
}

