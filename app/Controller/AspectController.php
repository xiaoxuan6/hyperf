<?php

declare(strict_types=1);

namespace App\Controller;

use App\Annotations\UserAnnotation;
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * @AutoController()
 * @UserAnnotation(age="10",name="vinshon")
 */
class AspectController
{
    public $name = "eto";

    public function index()
    {
        return 1;
    }

    public function get($name = "sdfsd")
    {
        return __METHOD__ . $name;
    }
}
