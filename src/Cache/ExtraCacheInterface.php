<?php
namespace Mvc4us\Cache;

/**
 *
 * @author erdem
 *
 */
interface ExtraCacheInterface extends CacheInterface
{

    /**
     * Like the CacheInterface::get() fetches a value from the cache also resetting the expiration TTL time.
     *
     * @param string $key
     *            The unique key of this item in the cache.
     * @param mixed $var
     *            Variable to be set with returned value. Also acts as a default value if the key does not exist.
     * @param null|int|\DateInterval $ttl
     *            Optional. The TTL value of this item. If no value is sent and
     *            the driver supports TTL then the library may set a default value
     *            for it or let the driver take care of that.
     *
     * @return boolean True on success and false on failure.
     *
     * @throws \Mvc4us\Cache\InvalidArgumentException MUST be thrown if the $key string is not a legal value.
     */
    public function touch($key, &$var, $ttl = null): bool;

    /**
     * Like the CacheInterface::getItem() fetches a value from a hash table from the cache also resetting the expiration TTL time.
     *
     * @param string $key
     *            The unique key of this array in the cache.
     * @param string $memberKey
     *            The unique key of the item in this array.
     * @param mixed $var
     *            Variable to be set with returned value. Also acts as a default value if the key does not exist.
     * @param null|int|\DateInterval $ttl
     *            Optional. The TTL value of this item. If no value is sent and
     *            the driver supports TTL then the library may set a default value
     *            for it or let the driver take care of that.
     *
     * @return boolean True on success and false on failure.
     *
     * @throws \Mvc4us\Cache\InvalidArgumentException MUST be thrown if the $key string is not a legal value.
     */
    public function touchItem($key, $memberKey, &$var, $ttl = null): bool;
}

