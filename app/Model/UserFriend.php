<?php

declare (strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 */
class UserFriend extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_friends';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ["oauth_id", "user_id", "status"];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ["oauth_id" => "integer", "user_id" => "integer", "status" => "integer"];

    public function oauth()
    {
        return $this->belongsTo(Oauth::class, "oauth_id", "id");
    }
}