<?php

declare (strict_types=1);

namespace App\Model;

use Hyperf\Database\Model\Events\Saving;
use Hyperf\DbConnection\Model\Model;
use Hyperf\ModelCache\Cacheable;
use Hyperf\ModelCache\CacheableInterface;
use Qbhy\HyperfAuth\Authenticatable;

/**
 * @property int $id
 * @property string $name
 * @property int $age
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Oauth extends Model implements CacheableInterface, Authenticatable
{
    use Cacheable;

    /**
     * Notes: 重新设置过期时间
     * Date: 2021/4/2 16:49
     * @return int|null
     */
    public function getCacheTTL(): ?int
    {
        return 3600;
    }

    /**
     * Notes: 自定义删除缓存数据
     * Date: 2021/4/2 16:50
     * @return bool
     */
    public function deleteCache(): bool
    {
//        $this->getKey();
        return false;
    }

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Oauth';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ["name", "age", "password", "descirption", "class"];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'age' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', "descirption" => "array", "class" => "string"];

    /**
     * Notes: 模型观察者
     * Date: 2021/4/1 17:53
     * @param Saving $saving
     */
    public function saving(Saving $saving)
    {
        $this->name = $this->name . "_hyperf";
    }

    public function getId()
    {
        return $this->getKey();
    }

    public static function retrieveById($key): ?Authenticatable
    {
        return self::query()->whereKey($key)->first();
    }
}