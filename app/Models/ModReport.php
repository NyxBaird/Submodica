<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ModReport
 * @package App
 */
class ModReport extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mod_reports';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reporter_id',
        'mod_id',
        'reason',
        'details', //500 char limit
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

    public function reporter() {
        return $this->belongsTo(User::class, 'reporter_id');
    }
}
