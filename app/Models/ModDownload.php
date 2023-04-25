<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ModDownload
 * @package App
 */
class ModDownload extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mod_downloads';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mod_id',
        'version',
        'url',
        'checksum',
        'title',
        'guid'
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

    public function updateChecksum() {
        $this->checksum = md5_file($this->url);
        $this->save();
    }
}
