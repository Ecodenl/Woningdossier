<?php

namespace App\Helpers\Sanitizers;

class HtmlSanitizer implements Sanitizer
{
    /**
     * @param $input
     */
    public function sanitize($input): ?string
    {
        // Separate lines for clarity
        $input = preg_replace('/<script.*?>(.*)?<\/script>/ism', '', $input); // Remove script tags
        $input = preg_replace('/on[a-zA-Z]*="[^"]*?"/ism', '', $input); // Remove inline event handlers
        $input = preg_replace('/<img[^>]*>/ism', '', $input); // Remove images
        $input = preg_replace('/<video.*?>(.*)?<\/video>/ism', '', $input); // Remove videos
        $input = preg_replace("/(<p><br><\/p>){2,}/ism", '<p><br></p>', $input); // Remove double or more empty lines
        $input = preg_replace('/<p> *<\/p>/ism', '', $input); // Remove empty paragraphs
        $input = preg_replace('/(<a.*?)(rel=[\'"][^\'"]*[\'"])(.*?>.*?<\/a>)/ism', '$1$3', $input); // Remove any anchor rel (we will add them ourselves)
        $input = preg_replace('/(<a.*?)(href=[\'"][^\/][^\'"]*[\'"])(.*?>.*?<\/a>)/ism', '$1$2 rel="nofollow" $3', $input); // Add rel="nofollow" to each external anchor
        $input = preg_replace('/<iframe[^>]*?(?:\/>|>[^<]*?<\/iframe>)/ism', '', $input); // Remove iFrames

        // Remove all style tags if they don't begin with an allowed style tag.
        // NOTE: This isn't perfect. Something like 'style="font-size: 14px; color: red;"' will remain untouched,
        // but it should at least clean out the biggest garbage.
        $input = preg_replace('/(style=\"((?!font-size|list-style-type|text-decoration)[^"]*)\")/', '', $input);

        // Trim the end content
        $input = trim($input);

        // If it's just a new line, it's as good as null
        if ($input === '<p><br></p>' || $input === '<br>' || $input === '<p></p>') {
            $input = null;
        }

        return $input;
    }
}
