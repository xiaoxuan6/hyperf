<?php

declare(strict_types=1);

namespace App\Controller;

use App\Annotations\UserAnnotation;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Utils\Arr;

/**
 * @AutoController(prefix="/an")
 * @UserAnnotation(name="eto", age="10")
 */
class AnnotationController
{
    /**
     * @UserAnnotation(name = "上海")
     */
    protected $name;

    protected $address = "vinshon";

    public function index(RequestInterface $request, ResponseInterface $response)
    {
        var_dump(Arr::get(AnnotationCollector::getContainer(), self::class));

        var_dump("=========== getClassesByAnnotation ===========");
        $userObjs = AnnotationCollector::getClassesByAnnotation(UserAnnotation::class);
//        var_dump($userObjs);
        $userObj = current($userObjs);
        var_dump($userObj->name); // 这里获取的是当前类上面的变量

        var_dump(PHP_EOL . "=========== getClassAnnotation ===========");
        $userObj = AnnotationCollector::getClassAnnotation(self::class, UserAnnotation::class);
//        var_dump($userObj);
        var_dump($userObj->name); // 这里获取的是当前类上面的变量

        var_dump(PHP_EOL . "=========== getClassMethodAnnotation ===========");
        $userObjs = AnnotationCollector::getClassMethodAnnotation(self::class, "getAddress");
//        var_dump($userObjs);
        $userObj = current($userObjs);
        var_dump($userObj->name); // 这里获取的是当前类方法 getAddress 上面的变量

        var_dump(PHP_EOL . "=========== getClassMethodAnnotation ===========");
        $userObjs = AnnotationCollector::getClassPropertyAnnotation(self::class, "name");
//        var_dump($userObjs);
        $userObj = current($userObjs);
        var_dump($userObj->name); // 这里获取的是当前类 name 的变量

        return $response->raw('Hello Hyperf!');
    }

    public function getMethod()
    {
        $obj = AnnotationCollector::getClassMethodAnnotation(self::class, "getAddress");
        var_dump($obj);

        return success("ok");
    }

    /**
     * @UserAnnotation(name="vinshon")
     */
    protected function getAddress()
    {
        return $this->address;
    }
}
