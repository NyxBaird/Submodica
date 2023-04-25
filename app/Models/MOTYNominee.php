<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MOTYNominee
 * @package App
 */
class MOTYNominee extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'moty_nominees';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime:m-d-Y',
        'updated_at' => 'datetime:m-d-Y',
    ];
}
