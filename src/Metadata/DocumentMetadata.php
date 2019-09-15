<?php

namespace Vaites\ApacheTika\Metadata;

use DateTime;
use DateTimeZone;

use Vaites\ApacheTika\Metadata\Mixins\Document;

/**
 * Metadata class for documents
 *
 * @author  David Martínez <contacto@davidmartinez.net>
 */
class DocumentMetadata extends Metadata
{
    use Document;

    // $timezone = new DateTimeZone('UTC');
    // if(is_array($value))
    // {
    //     $value = array_shift($value);
    // }
    // $keywords = preg_split(preg_match('/,/', $value) ? '/\s*,\s*/' : '/\s+/', $value);
    // $this->keywords = array_unique($keywords ?: []);
    //
    // $this->language = mb_substr($value, 0, 2);
    //
    // $mime = $value ? preg_split('/;\s+/', $value) : [];
    // $this->mime = array_shift($mime);
    //
    // $value = preg_replace('/\$.+/', '', $value);
    // $this->generator = trim($value);
    //
    //
    // $value = preg_replace('/\.\d+/', 'Z', $value);
    // $this->created = new DateTime($value, $timezone);
    //
    // $value = preg_replace('/\.\d+/', 'Z', $value);
    // $this->updated = new DateTime($value, $timezone);
    //
    // $this->encoding = $value;
}
