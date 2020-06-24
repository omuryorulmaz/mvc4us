<?php
namespace Mvc4us\Cache;

/**
 *
 * @author erdem
 *
 */
interface ExtendedCacheInterface extends CacheInterface
{

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
     * @throws \Mvc4us\Cache\Exception\InvalidArgumentException MUST be thrown if the $key string is not a legal value.
     */
    public function touchItem($key, $memberKey, &$var, $ttl = null): bool;

    /**
     * Fetches a value from a hash table from the cache.
     *
     * @param string $key
     *            The unique key of this array in the cache.
     * @param string $memberKey
     *            The unique key of this item in the array.
     * @param mixed $var
     *            Variable to be set with returned value. Also acts as a default value if the key does not exist.
     *
     * @return bool True on success and false on failure.
     *
     * @throws \Mvc4us\Cache\Exception\InvalidArgumentException MUST be thrown if the $key and/or $memberKey string is not a legal value.
     */
    public function getItem($key, $memberKey, &$var): bool;

    /**
     * Persists data into a hash table in the cache, uniquely referenced by a key with an optional expiration TTL time.
     *
     * @param string $key
     *            The key of the item to store.
     * @param mixed $value
     *            The value of the item to store, must be serializable.
     * @param null|int|\DateInterval $ttl
     *            Optional. The TTL value of this item. If no value is sent and
     *            the driver supports TTL then the library may set a default value
     *            for it or let the driver take care of that.
     *
     * @return bool True on success and false on failure.
     *
     * @throws \Mvc4us\Cache\Exception\InvalidArgumentException MUST be thrown if the $key and/or $memberKey string is not a legal value.
     */
    public function setItem($key, $memberKey, $value, $expiration = null): bool;

    /**
     * Delete an item from a hashtable from the cache by its unique key.
     *
     * @param string $key
     *            The unique cache key of the item to delete.
     *
     * @return bool True if the item was successfully removed. False if there was an error.
     *
     * @throws \Mvc4us\Cache\Exception\InvalidArgumentException MUST be thrown if the $key and/or $memberKey string is not a legal value.
     */
    public function deleteItem($key, $memberKey): bool;

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
     * @throws \Mvc4us\Cache\Exception\InvalidArgumentException MUST be thrown if the $key string is not a legal value.
     */
    public function touch($key, &$var, $ttl = null): bool;

    public function getTimeLeft($key): int;

    public function setExpire($key, $expiration = null): bool;
}

