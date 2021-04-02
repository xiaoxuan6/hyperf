<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2021/3/31
 * Time: 18:53
 */

namespace App\Utils\Facade;

use Hyperf\Contract\ConfigInterface;

/**
 * Class Cache
 * @package App\Utils\Facade
 *
 * @method static get(string $key, $default = null)
 * @method static set(string $key, $value)
 * @method static has(string $key)
 *
 * @return ConfigInterface
 */
class Config extends Facade
{
    public static $container = ConfigInterface::class;
}