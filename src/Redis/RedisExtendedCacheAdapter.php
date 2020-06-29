<?php
namespace Mvc4us\Redis;

use Mvc4us\Cache\ExtendedCacheInterface;

/**
 *
 * @author erdem
 *
 */
class RedisExtendedCacheAdapter extends RedisCacheAdapter implements ExtendedCacheInterface
{

    /**
     *
     * @see \Mvc4us\Cache\ExtendedCacheInterface::getItem()
     */
    public function getItem($key, $memberKey, &$var): bool
    {
        if (! $this->hasItem($key, $memberKey)) {
            $this->lastFound = false;
            return $this->lastFound;
        }

        $value = $this->redis->hGet($this->key($key), $memberKey);
        $type = gettype($var);
        if ('object' === $type) {
            if ($this->sameObjectType($var, $value)) {
                $var = $value;
                $this->lastFound = true;
                return $this->lastFound;
            }
            $this->lastFound = false;
            return $this->lastFound;
        }

        $var = $value;
        $this->lastFound = true;
        return $this->lastFound;
    }

    /**
     *
     * @see \Mvc4us\Cache\ExtendedCacheInterface::setItem()
     */
    public function setItem($key, $memberKey, $value, $expiration = null): bool
    {
        if (false === $this->redis->hSet($this->key($key), $memberKey, $value)) {
            return false;
        }

        $ttl = $this->getTimeLeft($key);
        if ($ttl > 0) {
            return true;
        }

        if (! $this->setExpire($key, $expiration)) {
            return false;
        }
        return true;
    }

    /**
     *
     * @see \Mvc4us\Cache\ExtendedCacheInterface::deleteItem()
     */
    public function deleteItem($key, $memberKey): bool
    {
        return $this->redis->hDel($this->key($key), $memberKey);
    }

    /**
     *
     * @see \Mvc4us\Cache\ExtendedCacheInterface::touch()
     */
    public function touch($key, &$var, $ttl = null): bool
    {
        if ($this->get($key, $var)) {
            if ($this->setExpire($key, $ttl)) {
                $this->lastFound = true;
                return $this->lastFound;
            }
        }
        $this->lastFound = false;
        return $this->lastFound;
    }

    /**
     *
     * @see \Mvc4us\Cache\ExtendedCacheInterface::touchItem()
     */
    public function touchItem($key, $memberKey, &$var, $ttl = null): bool
    {
        if ($this->getItem($key, $memberKey, $var)) {
            if ($this->setExpire($key, $ttl)) {
                $this->lastFound = true;
                return $this->lastFound;
            }
        }
        $this->lastFound = false;
        return $this->lastFound;
    }

    public function hasItem($key, $memberKey): bool
    {
        return $this->redis->hExists($this->key($key), $memberKey);
    }

    public function getTimeLeft($key): int
    {
        $ttl = $this->redis->ttl($this->key($key));
        if ($ttl < 0) {
            return false;
        }
        return $ttl;
    }

    public function setExpire($key, $expiration = null): bool
    {
        return $this->redis->expire($this->key($key), $this->checkExpiration($expiration));
    }
}

