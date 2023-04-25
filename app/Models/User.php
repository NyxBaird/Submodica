<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Our User Model
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'discord_id',
        'github_id',
        'role_id',
        'banned',
        'donation_link'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime:m-d-Y h:ia',
        'updated_at' => 'datetime:m-d-Y h:ia',
    ];
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tokens()
    {
        return $this->hasMany(Token::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mods() {
        return $this->hasMany(Mod::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function flags() {
        return $this->hasMany(UserFlag::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function meta()
    {
        return $this->hasMany(UserMeta::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function githubData()
    {
        return $this->hasOne(GithubData::class, "github_id", "github_id");
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function discordData()
    {
        return $this->hasOne(DiscordData::class, "discord_id", "discord_id");
    }

    /*
     * User Meta Stuff
     */

    /**
     * @param $key
     * @param $default
     * @return mixed
     */
    public function getMeta($key, $default)
    {
        $entry = UserMeta::where("user_id", $this->id)->where("key", $key)->first();
        return $entry?$entry->value:$default;
    }

    /**
     * @param $key
     * @param $value
     * @return bool
     */
    public function setMeta($key, $value)
    {
        if (!$entry = UserMeta::where("user_id", $this->id)->where("key", $key)->first())
            $entry = new UserMeta([
                "user_id" => $this->id,
                "key" => $key
            ]);

        $entry->value = $value;

        return $entry->save();
    }

    /**
     * @param $key
     * @return mixed
     */
    public function deleteMeta($key)
    {
        $entry = UserMeta::where("user_id", $this->id)->where("key", $key)->first();

        return $entry->delete();
    }

    /**
     * @return mixed
     */
    public static function getBannedIds() {
        return User::where("banned", true)->pluck("id")->toArray();
    }
}
