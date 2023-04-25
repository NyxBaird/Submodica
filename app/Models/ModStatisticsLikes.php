<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ModStatisticsLikes
 * @package App
 */
class ModStatisticsLikes extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mod_statistics_likes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mod_id',
        'user_id'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime:m-d-Y',
        'updated_at' => 'datetime:m-d-Y',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mod() {
        return $this->belongsTo(Mod::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo(User::class);
    }
}
