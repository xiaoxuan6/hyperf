<?php

declare (strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 */
class UserChatList extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_chat_lists';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ["uid", "friend_id"];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ["uid" => "integer", "friend_id" => "integer", "updated_at" => "datetime"];

    public function uidUserInfo()
    {
        return $this->belongsTo(Oauth::class, "uid", "id")->select(["id", "name"]);
    }

    public function friendUserInfo()
    {
        return $this->belongsTo(Oauth::class, "friend_id", "id")->select(["id", "name"]);
    }
}