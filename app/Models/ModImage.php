<?php
namespace App\Models;

use App\Http\Services\Respond;
use App\Libraries\BunnyCDN\BunnyCDN;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ModImage
 * @package App
 */
class ModImage extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mod_images';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mod_id',
        'source',
        'type',     //cover, icon, gallery
        'adult',    // 0 = false, 1 - 5 Google AI Rating
        'medical',  //...
        'spoof',    //...
        'violence'  //...
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

    public function delete()
    {
        if (env('APP_ENV') !== 'production')
            return $this->deleteLocalOnly();

        $cdn = new BunnyCDN('submodica', env('BUNNY_KEY'), 'la');
        $response = $cdn->deleteObject("/submodica" . str_replace("https://submodica.b-cdn.net", "", $this->source));
        if ($response->HttpCode == 200) {
            $this->delete();

            if (in_array($this->type, ['icon', 'cover']) && $this->mod->creation_status === 'live') {
                $this->mod->creation_status = 'downloads';
                $this->mod->save();
            }

            parent::delete();

            return true;
        } else
            return false;
    }

    public function deleteLocalOnly()
    {
        parent::delete();
    }
}
