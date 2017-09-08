<?php
namespace Umurkaragoz\Metaman;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Meta
 *
 * @property string $content_type
 * @property int $content_id
 * @property string $meta_name
 * @property string $meta_content
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $content
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Meta whereContentId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Meta whereContentType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Meta whereMetaContent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Meta whereMetaName($value)
 * @mixin \Eloquent
 */
class Meta extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'meta';

    public $fillable = ['meta_name', 'meta_content', 'content_type', 'content_id'];

    public $timestamps = false;

    /* ------------------------------------------------------------------------------------------------------------------------------ RELATIONS -+- */
    /**
     * @return Model|MorphTo
     */
    public function content()
    {
        return $this->morphTo();
    }

    /* --------------------------------------------------------------------------------------------------------------------------------- SCOPES -+- */

    /* ----------------------------------------------------------------------------------------------------------------------------- ATTRIBUTES -+- */

    /* -------------------------------------------------------------------------------------------------------------------------------- HELPERS -+- */
}
