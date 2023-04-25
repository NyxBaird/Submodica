<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class GithubData
 * @package App\Models
 */
class GithubData extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_github_data';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'login',
        'github_id',
        'node_id',
        'avatar_url',
        'gravatar_id',
        'url',
        'html_url',
        'followers_url',
        'following_url',
        'gists_url',
        'starred_url',
        'subscriptions_url',
        'organizations_url',
        'repos_url',
        'events_url',
        'received_events_url',
        'type',
        'email',
        'twitter_username',
        'public_repos',
        'public_gists',
        'followers',
        'following',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'github_id', 'github_id');
    }
}
