<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DevLog
 * @package App
 */
class DevLog extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dev_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'data'
    ];
}
