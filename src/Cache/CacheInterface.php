<?php
namespace Mvc4us\Cache;

/**
 *
 * @author erdem
 *
 */
interface CacheInterface
{

    /**
     * Fetches a value from the cache.
     *
     * @param string $key
     *            The unique key of this item in the cache.
     * @param mixed $var
     *            Variable to be set with returned value. Also acts as a default value if the key does not exist.
     *
     * @return bool True on success and false on failure.
     *
     * @throws \Mvc4us\Cache\Exception\InvalidArgumentException MUST be thrown if the $key string is not a legal value.
     */
    public function get($key, &$var): bool;

    /**
     * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
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
     * @throws \Mvc4us\Cache\Exception\InvalidArgumentException MUST be thrown if the $key string is not a legal value.
     */
    public function set($key, $value, $ttl = null): bool;

    /**
     * Delete an item from the cache by its unique key.
     *
     * @param string $key
     *            The unique cache key of the item to delete.
     *
     * @return bool True if the item was successfully removed. False if there was an error.
     *
     * @throws \Mvc4us\Cache\Exception\InvalidArgumentException MUST be thrown if the $key string is not a legal value.
     */
    public function delete($key): bool;

    /**
     * Wipes clean the entire cache's keys.
     *
     * @return bool True on success and false on failure.
     *         <<<<<<< Updated upstream
     *
     * @throws \Mvc4us\Cache\Exception\InvalidArgumentException MUST be thrown if the $key and/or $memberKey string is not a legal value.
     *         =======
     *         >>>>>>> Stashed changes
     */
    public function clear(): bool;

    /**
     * Determines whether an item is present in the cache.
     *
     * NOTE: It is recommended that has() is only to be used for cache warming type purposes
     * and not to be used within your live applications operations for get/set, as this method
     * is subject to a race condition where your has() will return true and immediately after,
     * another script can remove it making the state of your app out of date.
     *
     * @param string $key
     *            The cache item key.
     *
     * @return bool
     * @throws \Mvc4us\Cache\Exception\InvalidArgumentException MUST be thrown if the $key string is not a legal value.
     */
    public function has($key): bool;

    /**
     * Determines when an item is not found when searched from cache.
     *
     *
     * @param
     *
     * @return bool
     * @throws \Mvc4us\Cache\Exception\InvalidArgumentException MUST be thrown if the $key and/or $memberKey string is not a legal value.
     */
    public function notFound(): bool;

    /**
     * Determines when an item is found when searched from cache.
     *
     *
     * @param
     *
     * @return bool
     *
     */
    public function found(): bool;

    /**
     * Determines which cache an item comes from.
     *
     *
     * @param
     *
     * @return string
     * @throws \Mvc4us\Cache\Exception\InvalidArgumentException MUST be thrown if the $key string is not a legal value.
     */
    public function getPrefix(): string;

    /**
     *
     * @param string $prefix
     *
     *
     * @throws \Mvc4us\Cache\Exception\InvalidArgumentException MUST be thrown if the $key string is not a legal value.
     */
    public function setPrefix($prefix);
}

