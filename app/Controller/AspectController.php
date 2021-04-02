<?php

declare(strict_types=1);

namespace App\Controller;

use Hyperf\HttpServer\Annotation\AutoController;

/**
 * @AutoController()
 */
class AspectController
{
    public $name = "eto";

    public function index()
    {
        return 1;
    }
}
