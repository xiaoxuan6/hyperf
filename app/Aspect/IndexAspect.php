<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2021/3/28
 * Time: 16:32
 */

namespace App\Aspect;

use App\Controller\AspectController;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;

/**
 * @Aspect()
 */
class IndexAspect extends AbstractAspect
{
    public $classes = [
        AspectController::class
    ];

    /**
     * @return mixed return the value from process method of ProceedingJoinPoint, or the value that you handled
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        // TODO: Implement process() method.
        $result = $proceedingJoinPoint->process();

        // 获取闭包所在的对象
        $method = $proceedingJoinPoint->originalMethod;
//        $object = (new \ReflectionFunction($method))->getClosure();
        $object = (new \ReflectionFunction($method))->getClosureThis();
        $name = $object->name;

//        $proceedingJoinPoint->className;
        return __CLASS__ . ":" . $result . "_" . $name;
    }
}