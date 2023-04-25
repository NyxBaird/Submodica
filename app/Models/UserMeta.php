<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *
 */
class UserMeta extends Model
{
    /**
     * @var string
     */
    protected $table = 'user_metadata';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'key',
        'value'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
