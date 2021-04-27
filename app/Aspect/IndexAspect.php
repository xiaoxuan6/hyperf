<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2021/3/28
 * Time: 16:32
 */

namespace App\Aspect;

use App\Annotations\UserAnnotation;
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

    public $annotations = [
        UserAnnotation::class
    ];

    /**
     * @return mixed return the value from process method of ProceedingJoinPoint, or the value that you handled
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        // TODO: Implement process() method.
        $result = $proceedingJoinPoint->process();

        // 获取当前类名
//        $class = $proceedingJoinPoint->className;
//        var_dump($class);
//
        // 获取 class 类所在的 object
//        $annotation = $proceedingJoinPoint->getReflectMethod();
//        $class = $annotation->class;
//        $name = $annotation->name;
//        var_dump($class, $name);
//        var_dump($annotation);

        // 获取闭包所在的对象
//        $method = $proceedingJoinPoint->originalMethod;
//        var_dump($method);
//        $object = (new \ReflectionFunction($method))->getClosure();
//        $object = (new \ReflectionFunction($method))->getClosureThis();

        // 获取 class 中的属性 name
//        $object = $proceedingJoinPoint->getInstance();
//        $name = $object->name;
//        var_dump($object);

        // 获取方法中的参数
//        $arg = $proceedingJoinPoint->getArguments();
//        var_dump($arg);

        // 获取类中的注解
//        $data = $proceedingJoinPoint->getAnnotationMetadata()->class;
//        $annotation = $data[UserAnnotation::class];
//        var_dump($annotation->name);

        return __CLASS__ . ":" . $result;
    }
}