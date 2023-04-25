<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DocumentationFile
 * @package App
 */
class DocumentationFile extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'documentation_files';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'path',
        'estimated_tokens'
    ];
}
