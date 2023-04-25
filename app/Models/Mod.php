<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Mod
 * @package App
 */
class Mod extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mods';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'creator',
        'title',
        'tagline',
        'game', //sbz or sn1
        'description',
        'misc_attributions',
        'repo_id',
        'repo_url',
        'views',
        'downloads',
        'latest_version',
        'creation_status', //this should be the last mod pane submitted by the user or else "live"
        'subnautica_compatibility', //The rough version of Subnautica this mod is compatible with
        'show_cover_title',
        'show_donation_link',
        'donation_link'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime:M d Y',
        'updated_at' => 'datetime:M d Y',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags() {
        return $this->belongsToMany(Tag::class, 'mod_tags');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attributions()
    {
        return $this->hasMany(ModAttribution::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function modStatisticDownloadsUnique()
    {
        return $this->hasMany(ModStatisticsDownloadsUnique::class);
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function modStatisticViewsUnique()
    {
        return $this->hasMany(ModStatisticsViewsUnique::class);
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function modStatisticLikes()
    {
        return $this->hasMany(ModStatisticsLikes::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images() {
        return $this->hasMany(ModImage::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function downloadLinks() {
        return $this->hasMany(ModDownload::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments() {
        return $this->hasMany(ModComment::class);
    }

    protected function creationStatus(): Attribute
    {
        return Attribute::make(get:
            fn ($value) => empty($value) ? "index" : $value,
        );
    }

    public function delete()
    {
        $this->tags()->detach();
        $this->attributions()->delete();
        $this->images()->delete();
        $this->downloadLinks()->delete();
        $this->modStatisticDownloadsUnique()->delete();
        $this->modStatisticViewsUnique()->delete();
        parent::delete();
    }

    public function updateStatus($newStatus) {
        $statusRankings = [
            '' => 0,
            'index' => 1,
            'attributions' => 2,
            'downloads' => 3,
            'preview' => 4,
            'live' => 5
        ];

        if ($newStatus === 'preview' || $statusRankings[$newStatus] > $statusRankings[$this->creation_status]) {
            $this->creation_status = $newStatus;
            $this->save();
        }
    }
}
