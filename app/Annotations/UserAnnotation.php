<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2021/4/26
 * Time: 11:20
 */

namespace App\Annotations;

use Hyperf\Di\Annotation\AbstractAnnotation;

/*
 * @Target 主要用于指定该注解默认的级别是 类注解、还是方法、或者是类成员属性
 * 可以看到定义注解其实就是在 注释上增加两个类似 "方法" 的东西注意注解类的 @Annotation 和 @Target 注解为全局注解，所以无需 use ，引入命名空间
 */

/**
 * @Annotation
 * @Target("ALL")
 */
class UserAnnotation extends AbstractAnnotation
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    protected $age;

    public function __construct($value = null)
    {
        parent::__construct($value);

        $this->bindMainProperty("name", $value);
    }

}
