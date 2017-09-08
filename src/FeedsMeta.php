<?php
namespace Umurkaragoz\Metaman;

use Metaman;
use Input;

trait FeedsMeta
{

    /* ------------------------------------------------------------------------------------------------------------------------------ LISTENERS -+- */
    public static function bootMetaTrait()
    {
        self::saving(function ($_this) {
            if ($_this->exists && $_this->metaAutoUpdate !== false) {
                $_this->syncMeta();
            }
        });
        self::created(function ($_this) {
            if ($_this->metaAutoUpdate !== false) {
                $_this->syncMeta();
            }
        });
    }

    /* ------------------------------------------------------------------------------------------------------------------------------ RELATIONS -+- */
    public function meta()
    {
        return $this->morphMany('Umurkaragoz\Metaman\Meta', 'content');
    }

    /* -------------------------------------------------------------------------------------------------------------------------------- HELPERS -+- */
    public function syncMeta($metaAttributes = null, $noDelete = false)
    {
        // if meta attributes array is not given, get it from the input
        if (!$metaAttributes) {
            $metaAttributes = Input::get('meta');
        }

        // validate the input type, return false if it is not an array.
        if (!is_array($metaAttributes)) return false;

        foreach ($metaAttributes as $type => $content) {
            // if the content is null, delete meta record with given type
            if ($content == null) {
                if (!$noDelete) {
                    $this->meta()->where('meta_name', $type)->delete();
                }
                continue;
            }

            // check if meta of this type exists for model
            $meta = $this->meta()->firstOrNew(['meta_name' => $type]);

            if ($meta->exists) {
                // if meta record with given type exists for this model, check if given content is up-to-date.
                // if not, update it. else do nothing.
                if ($meta->meta_content != $content) {
                    Meta::where([
                        'content_type' => $meta->content_type,
                        'content_id'   => $meta->content_id,
                        'meta_name'    => $meta->meta_name
                    ])->update([
                        'meta_content' => $this->sanitizeMetaInput($content)
                    ]);
                }
            } else {
                // if meta record with given type is not exists for this model, create new meta record with given content.
                $meta->create([
                    'content_type' => $meta->content_type,
                    'content_id'   => $meta->content_id,
                    'meta_name'    => $meta->meta_name,
                    'meta_content' => $content
                ]);
            }
        }
    }

    private function sanitizeMetaInput($input)
    {
        return str_replace([
            "\n", "\r"
        ], [
            "", ""
        ],
            $input);
    }

    /**
     * Returns meta data associated with this item
     *
     * @param null $name meta name requested. An array contains all meta name-content pairs will be returned if this left null.
     * @param null $default optional default value to be returned if no entry for requested meta name found.
     * @return string|array
     */
    public function getMeta($name = null, $default = null)
    {
        static $meta = null;

        if ($meta === null) {
            $meta = $this->meta->pluck('meta_content', 'meta_name')->toArray();
        }
        if (!$name) {
            return $meta;
        } else {
            return array_get($meta, $name, $default);
        }
    }

    /**
     * Feed the Meta Renderer with this model's meta. Optionally extend model's meta with input.
     *
     * @param null|array $input will extend/overwrite model's meta keys if provided.
     * @return $this
     */
    public function feedMeta($input = null)
    {
        $meta = is_array($input) ? array_merge($this->getMeta(), $input) : $this->getMeta();

        Metaman::feed(array_merge($this->metaDefaults(), $meta));

        return $this;
    }

    /**
     * You should override this function in your model to have meta defaults feature.
     * Default values will be overwritten by actual values if they exist.
     * This function must return an array. Array should have meta names and contents as keys and values
     *
     * example: [
     *      'description' => $this->some_field
     *      'keywords'    => someFunction()
     *      'author'      => 'some value'
     * ]
     *
     * @return array
     */
    protected function metaDefaults()
    {
        return [];
    }

}
