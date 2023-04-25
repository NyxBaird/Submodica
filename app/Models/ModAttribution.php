<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ModAttribution
 * @package App
 */
class ModAttribution extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mod_attributes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mod_id',
        'local_attribution_id',
        'name',
        'url', //2048 limit
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

    public function attributedMod() {
        return $this->belongsTo(Mod::class, 'local_attribution_id');
    }
}
