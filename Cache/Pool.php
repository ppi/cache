<?php
/**
 * This file is part of the PPI Framework.
 *
 * @copyright  Copyright (c) 2011-2013 Paul Dragoonis <paul@ppi.io>
 * @license    http://opensource.org/licenses/mit-license.php MIT
 * @link       http://www.ppi.io
 */

namespace PPI\Cache;

use Psr\Cache\PoolInterface;

/**
 * PPI Pool.
 *
 * @package    PPI
 * @subpackage Cache
 */
class Pool implements PoolInterface
{

    protected $driver;

    public function __construct($driver = null)
    {
        $this->driver = $driver;
    }

    /**
     * Set the driver for this cache pool
     *
     * @param $driver
     */
    public function setDriver($driver)
    {
        $this->driver = $driver;
    }

    /**
     * Get an item from the cache
     *
     * @param string $key
     * @return \Psr\Cache\ItemInterface
     */
    public function getItem($key)
    {
        return new CacheItem($this->driver, $key);
    }

    /**
     * Get items from the cache
     *
     * @param array $keys
     * @return \Traversable
     */
    public function getItems(array $keys)
    {
        $ret = array();
        foreach($keys as $key) {
            $ret[$key] = new CacheItem($this->driver, $key);
        }

        return $ret;
    }

    /**
     * Clear out the cache
     *
     * @return bool
     */
    public function clear()
    {
        return $this->driver->clear();
    }

}
