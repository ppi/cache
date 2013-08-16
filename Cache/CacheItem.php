<?php
/**
 * This file is part of the PPI Framework.
 *
 * @copyright  Copyright (c) 2011-2013 Paul Dragoonis <paul@ppi.io>
 * @license    http://opensource.org/licenses/mit-license.php MIT
 * @link       http://www.ppi.io
 */

namespace PPI\Cache;

use Psr\Cache\ItemInterface;

/**
 * CacheItem.
 *
 * @package    PPI
 * @subpackage Cache
 */
class CacheItem implements ItemInterface
{

    /**
     * @var \PPI\Cache\DriverInterface
     */
    protected $driver;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var bool
     */
    protected $isHit = false;

    /**
     * @var mixed
     */
    protected $value;

    protected $used = false;

    /**
     * Constructor.
     *
     * @param DriverInterface $driver
     * @param string $key
     */
    public function __construct($driver, $key)
    {
        $this->driver = $driver;
        $this->key    = $key;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Lets get items from the driver, we avoid duplicate gets() with the used param
     *
     * @return mixed
     */
    public function get()
    {

        list($value, $isHit) = $this->driver->get($this->getKey());
        $this->value = $value;
        $this->isHit = $isHit;

        return $this->value;
    }

    /**
     * @param null $value
     * @param null $ttl
     * @return bool
     */
    public function set($value = null, $ttl = null)
    {
        $this->isHit = true; // We just set it, so it exists
        $this->value = $value;
        return $this->driver->set($this->getKey(), $value, $ttl);
    }

    /**
     * @return bool
     */
    public function isHit()
    {
        return $this->isHit;
    }

    public function delete()
    {
        return $this->driver->remove($this->getKey());
    }
}
