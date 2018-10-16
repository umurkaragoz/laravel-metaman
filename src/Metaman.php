<?php

namespace Umurkaragoz\Metaman;

use InvalidArgumentException;

class Metaman
{
    private static $metaData = [];

    /**
     * Feed meta for the page.
     *
     * If the second parameter is supplied, parameters are name and content pair.
     * Otherwise the first parameter is key value pair, either array or object implementing FeedsMeta.
     *
     * @param $nameOrData    string|array|FeedsMeta
     * @param $content       string
     *
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    public static function feed($nameOrData, $content = null)
    {
        $meta = [];

        if ($content) {
            $meta[$nameOrData] = $content;
        } else {
            if (is_object($nameOrData)) {
                $meta = $nameOrData->meta()->pluck('meta_content', 'meta_name')->toArray();
            } else {
                $meta = $nameOrData;
            }
        }

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
            // use 'link' tag for canonical links
            if ($name == 'canonical') {
                $meta .= "    <link rel='canonical' href='$content'/>\n";
                continue;
            }

            $meta .= "    <meta$property name=\"$name\" content=\"$content\"/>\n";
        }

        return $meta;
    }
}