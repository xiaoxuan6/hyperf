<?php

declare (strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\DbConnection\Model\Model;

/**
 */
class ChatList extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'chat_lists';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ["oauth_id", "content", "receive_id"];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    public static function saveChatRecord($userId, $receive_id, $data)
    {
        return self::query()->insert([
            "oauth_id"   => $userId,
            "receive_id" => $receive_id,
            "content"    => $data,
            "created_at" => Carbon::now()->toDateTimeString(),
            "updated_at" => Carbon::now()->toDateTimeString(),
        ]);
    }
}