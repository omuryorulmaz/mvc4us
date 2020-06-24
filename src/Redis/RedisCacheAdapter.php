<?php
namespace Mvc4us\Redis;

use Mvc4us\Cache\CacheInterface;

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

    private $errorCode;

    private $errorString;

    private $prefix;

    private $lastFound;

    public function __construct(string $host, int $port = 6379, ?string $auth = null, int $db = 0)
    {
        $this->redis = new \Redis();

        if (! $this->redis->connect($host, $port)) {
            $this->errorString = sprintf('Connection failed to redis %s:%s', $host, $port);
            return;
        }

        $this->redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);

        if (! empty($auth)) {
            if (! $this->redis->auth($auth)) {
                $this->errorString = sprintf('Auth failed to redis %s:%s', $host, $port);
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
        if (! $this->exists($key)) {
            $this->lastFound = false;
            return $this->lastFound;
        }
        $type = gettype($var);
        if ('array' === $type) {
            $value = $this->redis->hGetAll($this->key($key));
            if (is_array($value)) {
                ksort($value);
                $var = $value;
                $this->lastFound = true;
                return $this->lastFound;
            }
            $this->lastFound = false;
            return $this->lastFound;
        }

        $value = $this->redis->get($this->key($key));
        if ('object' === $type) {
            if ($this->sameObjectType($var, $value)) {
                $var = $value;
                $this->lastFound = true;
                return $this->lastFound;
            }
            $this->lastFound = false;
            return $this->lastFound;
        }

        $var = $this->redis->get($key);
        $this->lastFound = true;
        return $this->lastFound;
    }

    /**
     *
     * @see \Mvc4us\Cache\CacheInterface::set()
     */
    public function set($key, $value, $ttl = null): bool
    {
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
        return $this->redis->delete($this->key($key));
    }

    /**
     *
     * @see \Mvc4us\Cache\CacheInterface::clear()
     */
    public function clear(): bool
    {
        $keys = $this->redis->getKeys($this->cachePrefix . '*');
        $flush = $this->redis->delete($keys);
        return count($keys) === $flush;
    }

    /**
     *
     * @see \Mvc4us\Cache\CacheInterface::has()
     */
    public function has($key): bool
    {
        return $this->redis->exists($this->key($key));
    }

    public function existsItem($key, $memberKey)
    {
        return $this->redis->hExists($this->key($key), $memberKey);
    }

    /**
     *
     * @see \Mvc4us\Cache\CacheInterface::getPrefix()
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     *
     * @see \Mvc4us\Cache\CacheInterface::setPrefix()
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     *
     * @see \Mvc4us\Cache\CacheInterface::found()
     */
    public function found(): bool
    {
        return $this->lastFound;
    }

    /**
     *
     * @see \Mvc4us\Cache\CacheInterface::notFound()
     */
    public function notFound(): bool
    {
        return ! $this->lastFound;
    }

    private function sameObjectType($o1, $o2)
    {
        $c1 = get_class($o1);
        if ($c1 === get_class($o2)) {
            return true;
        }
        return false;
    }

    private function key($key)
    {
        return $this->getCachePrefix() . $key;
    }

    private function checkExpiration($expiration)
    {
        if ($expiration < 0)
            return $this->expiration;
        return $expiration;
    }
}

