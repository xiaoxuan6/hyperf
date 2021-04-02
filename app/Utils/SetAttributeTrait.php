<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2021/4/2
 * Time: 11:44
 */

namespace App\Utils;

use Hyperf\Utils\Context;

Trait SetAttributeTrait
{
    static public function setName($name = "default")
    {
        Context::set("name", $name);

        return new static;
    }
}