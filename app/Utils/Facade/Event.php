<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2021/3/31
 * Time: 16:43
 */

namespace App\Utils\Facade;

use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class Event
 * @package App\Utils\Facade
 *
 * @method static dispatch(object $event)
 *
 * @return EventDispatcherInterface
 */
class Event extends Facade
{
    static public $container = EventDispatcherInterface::class;
}