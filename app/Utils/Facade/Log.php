<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2021/3/30
 * Time: 18:53
 */

namespace App\Utils\Facade;

use App\Utils\SetAttributeTrait;
use Hyperf\Logger\LoggerFactory;
use Psr\Log\LoggerInterface;

/**
 * Class Log
 * @package App\Utils
 *
 * @method static emergency($message, array $context = array())
 * @method static alert($message, array $context = array())
 * @method static critical($message, array $context = array())
 * @method static error($message, array $context = array())
 * @method static warning($message, array $context = array())
 * @method static notice($message, array $context = array())
 * @method static info($message, array $context = array())
 * @method static debug($message, array $context = array())
 * @method static log($message, array $context = array())
 *
 * @return LoggerInterface
 */
class Log extends Facade
{
    use SetAttributeTrait;

    static public $container = LoggerFactory::class;

    /**
     * Notes:
     * Date: 2021/3/31 16:31
     * @return null|string
     */
    static public function getConfig(): ?string
    {
        return "default";
    }

}