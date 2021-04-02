<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2021/3/31
 * Time: 17:14
 */

namespace App\Utils\Facade;

use App\Utils\SetAttributeTrait;
use Hyperf\AsyncQueue\Driver\DriverFactory;
use Hyperf\AsyncQueue\Driver\DriverInterface;
use Hyperf\AsyncQueue\JobInterface;

/**
 * Class AsyncQueue
 * @package App\Utils\Facade
 *
 * @method static push(JobInterface $job, int $delay = 0)
 * @method static delete(JobInterface $job)
 * @method static pop()
 * @method static ack($data)
 *
 * @return DriverInterface
 */
class AsyncQueue extends Facade
{
    use SetAttributeTrait;

    static public $container = DriverFactory::class;

    static public function getConfig(): ?string
    {
        return "default";
    }

}