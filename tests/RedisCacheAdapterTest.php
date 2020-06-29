<?php
namespace Mvc4us\Tests;

use Mvc4us\Redis\RedisCacheAdapter;
use PHPUnit\Framework\TestCase;

class RedisCacheAdapterTest extends TestCase
{

    public function testPrefix()
    {
        $redis = new RedisCacheAdapter(REDIS_HOST, REDIS_PORT, REDIS_AUTH);
        $prefix = 'RedisCache';
        $redis->setPrefix($prefix);
        $result = $redis->getPrefix();
        $this->assertEquals($prefix, $result);
    }

    public function testSetter()
    {
        $redis = new RedisCacheAdapter(REDIS_HOST, REDIS_PORT, REDIS_AUTH);
        $key = 'RedisCacheKey';
        $value = 'RedisCacheValue';
        $result = $redis->set($key, $value);
        $this->assertTrue($result);
    }

    public function testGetter()
    {
        $redis = new RedisCacheAdapter(REDIS_HOST, REDIS_PORT, REDIS_AUTH);
        $key = 'RedisCacheKey';
        $value = 'RedisCacheValue';
        $ttl = 30;
        $redis->set($key, $value, $ttl);
        $result = $redis->get($key, $value);
        $this->assertTrue($result);
    }

    public function testClear()
    {
        $redis = new RedisCacheAdapter(REDIS_HOST, REDIS_PORT, REDIS_AUTH);
        $result = $redis->clear();
        $this->assertTrue($result);
    }

    public function testNotFound()
    {
        $redis = new RedisCacheAdapter(REDIS_HOST, REDIS_PORT, REDIS_AUTH);
        $result = $redis->notFound();
        $this->assertTrue($result);
    }

    public function testFound()
    {
        $redis = new RedisCacheAdapter(REDIS_HOST, REDIS_PORT, REDIS_AUTH);
        $result = $redis->found();
        $this->assertFalse($result);
    }

    public function testHas()
    {
        $redis = new RedisCacheAdapter(REDIS_HOST, REDIS_PORT, REDIS_AUTH);
        $key = 'RedisCacheKey';
        $value = 'RedisCacheValue';
        $ttl = 30;
        $redis->set($key, $value, $ttl);
        $result = $redis->has($key);
        $this->assertTrue($result);
    }
}

