<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2021/3/26
 * Time: 17:54
 */

namespace App\Event;

use App\Model\Oauth;

class OauthEvent
{
    public $oauth;

    public function __construct(Oauth $oauth)
    {
        $this->oauth = $oauth;
    }
}