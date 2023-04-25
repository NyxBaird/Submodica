<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFlag extends Model
{
    /**
     * @var string
     */
    protected $table = 'user_flags';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'reason'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
