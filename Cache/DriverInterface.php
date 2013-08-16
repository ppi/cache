<?php

namespace PPI\Cache;

interface DriverInterface
{

    public function clear();

    public function get($key);

    public function set($key, $value = null, $ttl = null);

    public function remove($key);

//    public function getMultiple($keys);

//    public function setMultiple($keys, $ttl = null);

//    public function removeMultiple($keys);

}