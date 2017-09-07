<?php

namespace Umurkaragoz\Metaman;

class Metaman
{
    private static $metaData = [];

    /**
     * @param $input array|FeedsMeta
     *
     * @return bool
     */
    public static function feed($input)
    {
        if (is_object($input)) {
            $meta = $input->meta()->pluck('meta_content', 'meta_name')->toArray();
        } else {
            $meta = $input;
        }

        if (!is_array($meta)) {
            return false;
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

            $meta .= "    <meta$property name=\"$name\" content=\"$content\"/>\n";
        }
        return $meta;
    }
}