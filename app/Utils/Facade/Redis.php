<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2021/4/1
 * Time: 18:24
 */

namespace App\Utils\Facade;

use App\Utils\SetAttributeTrait;
use Hyperf\Redis\RedisFactory;

/**
 * Class Redis
 * @package App\Utils\Facade
 *
 * @method static set(string $key, $value)
 * @method static get(string $key)
 * @method static has(string $key)
 */
class Redis extends Facade
{
    use SetAttributeTrait;

    static public $container = RedisFactory::class;

    static public function getConfig(): ?string
    {
        return "default";
    }

}