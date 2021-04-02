<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2021/3/31
 * Time: 18:53
 */

namespace App\Utils\Facade;

use Psr\SimpleCache\CacheInterface;

/**
 * Class Cache
 * @package App\Utils\Facade
 *
 * @method static get($key, $default = null)
 * @method static set($key, $value, $ttl = null)
 * @method static delete($key)
 * @method static clear()
 * @method static getMultiple($keys, $default = null)
 * @method static setMultiple($values, $ttl = null)
 * @method static deleteMultiple($keys)
 * @method static has($key)
 *
 * @return CacheInterface
 */
class Cache extends Facade
{
    public static $container = CacheInterface::class;
}