<?php

/**
 * This file is part of the PPI Framework.
 *
 * @copyright  Copyright (c) 2011-2013 Paul Dragoonis <paul@ppi.io>
 * @license    http://opensource.org/licenses/mit-license.php MIT
 * @link       http://www.ppi.io
 */

include('ItemInterface.php');
include('PoolInterface.php');
include('DriverInterface.php');
include('Pool.php');
include('CacheItem.php');
include('Driver/ApcCache.php');

$exists = false;

$pool = new PPI\Cache\Pool();
$driver = new PPI\Cache\Driver\ApcCache();
$pool->setDriver($driver);

$item = $pool->getItem('paul');

// Lets get the value and check if it exists
var_dump('-- setting --');
$item->set('dragoonis');
$value = $item->get();
$exists = $item->isHit();
var_dump('exists', $exists, 'value', $value);

var_dump('-- deleting --');
// Lets delete it from the cache
$item->delete();

// Get the value and see if it exists (should be false)
$value = $item->get();
$exists = $item->isHit();
var_dump('exists', $exists, 'value', $value); exit;