<?php

namespace Umurkaragoz\Metaman;

use InvalidArgumentException;

class Metaman
{
    private static $metaData = [];

    /**
     * Feed meta for the page.
     *
     * If the second parameter is supplied, parameters must be name and content respectively.
     * If the second parameter is left null, the first parameter must be one of the below;
     * - array to supply a list of key value pairs,
     * - object implementing FeedsMeta trait, to feed modal meta,
     * - paginated eloquent query result, for feeding 'next' and 'previous' page tags for SEO,
     *
     * @param $nameOrOther    string|array|FeedsMeta|object
     * @param $content        string
     *
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    public static function feed($nameOrOther, $content = null)
    {
        $meta = [];

        if ($content) {
            $meta[$nameOrOther] = $content;
        } else {
            if (is_object($nameOrOther)) {
                if (method_exists($nameOrOther, 'previousPageUrl') && method_exists($nameOrOther, 'nextPageUrl')) {
                    // feed 'next' and 'previous' page tags for SEO
                    $meta = [
                        'prev' => $nameOrOther->previousPageUrl(),
                        'next' => $nameOrOther->nextPageUrl(),
                    ];
                } else {
                    // feed related meta rows from the database
                    $meta = $nameOrOther->meta()->pluck('meta_content', 'meta_name')->toArray();
                }
            } else {
                // feed by array of a list of key value pairs
                $meta = $nameOrOther;
            }
        }

        # - $meta is an array holding key and value pairs as meta name and content at this point.

        if (!is_array($meta)) {
            throw new InvalidArgumentException('Input must either be an array or a model using "FeedsMeta" trait.');
        }

        self::$metaData = array_merge(self::$metaData, $meta);
    }

    public static function render()
    {
        $meta = "\n";
        // extend project-wide defaults
        self::$metaData = array_merge(config('meta.defaults', []), self::$metaData);

        foreach (self::$metaData as $name => $content) {
            $property = '';
            if (strpos($name, 'og:') === 0) {
                $property = " property=\"$name\"";
            }

            // process variables/inner links
            if (strpos($content, ':') === 0) {
                $content = array_get(self::$metaData, substr($content, 1), false);
                if (!$content) continue;
            }

            // use 'title' tag instead if 'meta title'
            if ($name == 'title') {
                $meta .= "    <title>$content</title>\n";
                continue;
            }
            // use 'link' tag for 'prev', 'next' and 'canonical' SEO directives
            if (in_array($name, ['prev', 'next', 'content'])) {
                $meta .= "    <link rel='$name' href='$content'/>\n";
                continue;
            }

            $meta .= "    <meta$property name=\"$name\" content=\"$content\"/>\n";
        }

        return $meta;
    }
}