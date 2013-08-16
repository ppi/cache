<?php
/**
 * This file is part of the PPI Framework.
 *
 * @copyright  Copyright (c) 2011-2013 Paul Dragoonis <paul@ppi.io>
 * @license    http://opensource.org/licenses/mit-license.php MIT
 * @link       http://www.ppi.io
 */

namespace PPICacheModule\Cache\Driver;

use PPICacheModule\Cache\CacheInterface;
use PPICacheModule\Cache\CacheItem;

/**
 * Disk cache driver.
 *
 * @author     Paul Dragoonis <paul@ppi.io>
 * @package    PPI
 * @subpackage Cache
 */
class Disk implements CacheInterface
{

    /**
     * The folder where the cache contents will be placed
     *
     * @var string
     */
    protected $_cacheDir = null;

    /**
     * The options passed in upon instantiation
     *
     * @var array
     */
    protected $_options = array();

    public function __construct(array $options = array())
    {
        $this->_options  = $options;
        $this->_cacheDir = !empty($options['cache_dir']) ?
            $options['cache_dir'] : getcwd() . '/Cache/Disk/';
    }

    /**
     * @param  string $key
     *
     * @return \PPICacheModule\Cache\CacheItemInterface|CacheItem
     */
    public function get($key)
    {

        $exists = true;
        if (false === $this->exists($key)) {
            $exists = false;
        }

        $metaData = unserialize(file_get_contents($this->getKeyMetaCachePath($key)));
        $content  = file_get_contents($this->getKeyCachePath($key));
        $value    = $metaData['serialized'] ? unserialize($content) : $content;

        return new CacheItem($key, $value, $exists);
    }

    /**
     * Set a value in the cache.
     *
     * @param  string $key   The unique key for the cache item
     * @param  null   $value
     * @param  null   $ttl
     *
     * @throws \Exception
     * @return CacheItem
     */
    public function set($key, $value = null, $ttl = null)
    {

        $path     = $this->getKeyCachePath($key);
        $cacheDir = dirname($path);

        $this->remove($key);

        if (!is_dir($cacheDir)) {

            try {
                mkdir($cacheDir);
            } catch (\Exception $e) {
                throw new \Exception('Unable to create directory:<br>(' . $cacheDir . ')');
            }

        }

        if (false === is_writeable($cacheDir)) {

            $fileInfo = pathinfo(dirname($path));
            @chmod($cacheDir, 775);
            if (false === is_writable($cacheDir)) {
                throw new \Exception('Unable to create cache file: ' . $key . '. Cache directory not writeable.<br>(' . $this->_cacheDir . ')<br>Current permissions: ');
            }
        }

        $meta = array(
            'expire_time' => time() + (int)$ttl,
            'ttl'         => $ttl,
            'serialized'  => false
        );

        if (!is_scalar($value)) {
            $meta['serialized'] = true;
            $value              = serialize($value);
        }

        return file_put_contents($path, $value, LOCK_EX) > 0
               && file_put_contents($this->getKeyMetaCachePath($key), serialize($meta), LOCK_EX) > 0;
    }

    /**
     * @param  string $key
     *
     * @return bool|\string[]
     */
    public function remove($key)
    {
        if (file_exists(($path = $this->getKeyCachePath($key)))) {
            unlink($path);
            unlink($this->getKeyMetaCachePath($key));
        }
    }


    /**
     * @param  array $keys
     *
     * @return array
     */
    public function getMultiple($keys)
    {
    }

    /**
     * @param  array $keys
     * @param  null  $ttl
     *
     * @return array|bool
     */
    public function setMultiple($keys, $ttl = null)
    {
    }

    /**
     * @param  array $keys
     *
     * @return array|void
     */
    public function removeMultiple($keys)
    {
    }

    /**
     * @return bool
     */
    public function clear()
    {
    }

    /**
     * Check if a key exists in the cache
     *
     * @param string $key The Key(s)
     *
     * @return boolean
     */
    public function exists($key)
    {
        $path = $this->getKeyCachePath($key);
        if (false === file_exists($path)) {
            return false;
        }

        $meta = unserialize(file_get_contents($this->getKeyMetaCachePath($key)));

        // See if the item has a ttl and if it has expired then we delete it.
        if (is_array($meta) && $meta['ttl'] > 0 && $meta['expire_time'] < time()) {
            // Remove the cache item and its metadata file.
            $this->remove($key, true); // if we don't expect the existence, we could get an endless loop!
            return false;
        }

        return true;
    }

    /**
     * Get the full path to a cache item
     *
     * @param string $key
     *
     * @return string
     */
    protected function getKeyCachePath($key)
    {
        return $this->_cacheDir . $this->_options['prefix'] . 'default--' . $key;
    }

    /**
     * Get the full path to a cache item's metadata file
     *
     * @param string $key
     *
     * @return string
     */
    protected function getKeyMetaCachePath($key)
    {
        return $this->getKeyCachePath($key) . '.metadata';
    }

}
