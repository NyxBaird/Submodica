<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DiscordData
 * @package App\Models
 */
class DiscordData extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_discord_data';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'discord_id',
        'email',
        'username',
        'avatar',
        'discriminator',
        'public_flags',
        'flags',
        'banner',
        'banner_color',
        'accent_color',
        'locale',
        'verified'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'discord_id', 'discord_id');
    }
}
