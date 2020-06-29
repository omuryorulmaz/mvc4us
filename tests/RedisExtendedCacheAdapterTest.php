<?php
namespace Mvc4us\Tests;

use Mvc4us\Redis\RedisExtendedCacheAdapter;
use PHPUnit\Framework\TestCase;

class RedisExtendedCacheAdapterTest extends TestCase
{

    public function testSetItem()
    {
        $redis = new RedisExtendedCacheAdapter(REDIS_HOST, REDIS_PORT, REDIS_AUTH);
        $key = 'RedisExtendedCacheAdapterKey';
        $memberKey = 'RedisExtendedCacheAdapterMemberKey';
        $value = 'RedisExtendedCacheAdapterValue';
        $expiration = 30;
        $result = $redis->setItem($key, $memberKey, $value, $expiration);
        $this->assertTrue($result);
    }

    public function testGetItem()
    {
        $redis = new RedisExtendedCacheAdapter(REDIS_HOST, REDIS_PORT, REDIS_AUTH);
        $key = 'RedisExtendedCacheAdapterKey';
        $memberKey = 'RedisExtendedCacheAdapterMemberKey';
        $value = 'RedisExtendedCacheAdapterValue';
        $result = $redis->getItem($key, $memberKey, $value);
        $this->assertTrue($result);
    }

    public function testDeleteItem()
    {
        $redis = new RedisExtendedCacheAdapter(REDIS_HOST, REDIS_PORT, REDIS_AUTH);
        $key = 'RedisExtendedCacheAdapterKey';
        $memberKey = 'RedisExtendedCacheAdapterMemberKey';
        $result = $redis->deleteItem($key, $memberKey);
        $this->assertTrue($result);
    }

    public function testTouch()
    {
        $redis = new RedisExtendedCacheAdapter(REDIS_HOST, REDIS_PORT, REDIS_AUTH);
        $key = 'RedisExtendedCacheAdapterKey';
        $memberKey = 'RedisExtendedCacheAdapterMemberKey';
        $value = 'RedisExtendedCacheAdapterValue';
        $expiration = 30;
        $redis->setItem($key, $memberKey, $value, $expiration);
        $result = $redis->touch($key, $value, $expiration);
        $this->assertTrue($result);
    }

    public function testTouchItem()
    {
        $redis = new RedisExtendedCacheAdapter(REDIS_HOST, REDIS_PORT, REDIS_AUTH);
        $key = 'RedisExtendedCacheAdapterKey';
        $memberKey = 'RedisExtendedCacheAdapterMemberKey';
        $value = 'RedisExtendedCacheAdapterValue';
        $expiration = 30;
        $result = $redis->touchItem($key, $memberKey, $value, $expiration);
        $this->assertTrue($result);
    }

    public function testHasItem()
    {
        $redis = new RedisExtendedCacheAdapter(REDIS_HOST, REDIS_PORT, REDIS_AUTH);
        $key = 'RedisExtendedCacheAdapterKey';
        $memberKey = 'RedisExtendedCacheAdapterMemberKey';
        $value = 'RedisExtendedCacheAdapterValue';
        $expiration = 30;
        $redis->setItem($key, $memberKey, $value, $expiration);
        $result = $redis->hasItem($key, $memberKey);
        $this->assertTrue($result);
    }

    public function testGetTimeLeft()
    {
        $redis = new RedisExtendedCacheAdapter(REDIS_HOST, REDIS_PORT, REDIS_AUTH);
        $key = 'RedisExtendedCacheAdapterKey';
        $result = $redis->getTimeLeft($key);
        $this->assertIsNumeric($result);
    }

    public function testSetExpire()
    {
        $redis = new RedisExtendedCacheAdapter(REDIS_HOST, REDIS_PORT, REDIS_AUTH);
        $key = 'RedisExtendedCacheAdapterKey';
        $expiration = 30;
        $result = $redis->setExpire($key, $expiration);
        $this->assertTrue($result);
    }
}

