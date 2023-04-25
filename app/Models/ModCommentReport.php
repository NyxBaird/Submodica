<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ModCommentReport
 * @package App
 */
class ModCommentReport extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mod_comment_reports';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reporter_id',
        'comment_id',
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
    public function comment() {
        return $this->belongsTo(ModComment::class);
    }

    public function reporter() {
        return $this->belongsTo(User::class, 'reporter_id');
    }
}
