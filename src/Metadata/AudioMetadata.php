<?php

namespace Vaites\ApacheTika\Metadata;

use Vaites\ApacheTika\Metadata\Mixins\Album;
use Vaites\ApacheTika\Metadata\Mixins\Duration;
use Vaites\ApacheTika\Metadata\Mixins\Compression;

/**
 * Metadata class for audio files
 *
 * @author  David Martínez <contacto@davidmartinez.net>
 */
class AudioMetadata extends Metadata
{
    use Album;
    use Duration;
    use Compression;

    // $timezone = new DateTimeZone('UTC');
    // $this->language = mb_substr($value, 0, 2);
    // $this->duration = (int) ($value / 1000); // FIXME Milliseconds or seconds?
    // $this->releaseYear = (int) $value;
    // $this->discNumber = (int) $value;
    // $this->trackNumber = (int) $value;
    // $this->compilation = (bool) $value;
    // $value = preg_replace('/\.\d+/', 'Z', $value);
    // $this->created = new DateTime($value, $timezone);
    // $value = preg_replace('/\.\d+/', 'Z', $value);
    // $this->updated = new DateTime($value, $timezone);
    // $mime = $value ? preg_split('/;\s+/', $value) : [];
    // $this->mime = array_shift($mime);
    // $this->generator = trim($value);
}
