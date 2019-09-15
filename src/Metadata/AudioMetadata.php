<?php

namespace Vaites\ApacheTika\Metadata;

use DateTime;
use DateTimeZone;

/**
 * Metadata class for audio files
 *
 * @author  David Martínez <contacto@davidmartinez.net>
 */
class AudioMetadata extends Metadata
{
    /**
     * Title
     *
     * @var string
     */
    public $title = null;

    /**
     * Artist
     *
     * @var string
     */
    public $artist = null;

    /**
     * Album
     *
     * @var string
     */
    public $album = null;

    /**
     * Album artist
     *
     * @var string
     */
    public $albumArtist = null;

    /**
     * Composer
     *
     * @var string
     */
    public $composer = null;

    /**
     * Release year
     *
     * @var int
     */
    public $releaseYear = null;

    /**
     * Disc number
     *
     * @var int
     */
    public $discNumber = null;

    /**
     * Track number
     *
     * @var int
     */
    public $trackNumber = null;

    /**
     * Compilation
     *
     * @var bool
     */
    public $compilation = false;

    /**
     * Two-letter language code (ISO-639-1)
     *
     * @link https://en.wikipedia.org/wiki/ISO_639-1
     *
     * @var string
     */
    public $language = null;

    /**
     * Audio duration in seconds.
     *
     * @var int
     */
    public $duration = 0;

    /**
     * Software used to generate document
     *
     * @var string
     */
    public $generator = null;

    /**
     * Sets an attribute
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return bool
     */
    protected function setAttribute($key, $value)
    {
        $timezone = new DateTimeZone('UTC');

        switch(mb_strtolower($key))
        {
            case 'title':
                $this->title = $value;
                break;

            case 'xmpdm:artist':
                $this->artist = $value;
                break;

            case 'xmpdm:albumartist':
                $this->albumArtist = $value;
                break;

            case 'xmpdm:album':
                $this->album = $value;
                break;

            case 'xmpdm:composer':
                $this->composer = $value;
                break;

            case 'language':
                $this->language = mb_substr($value, 0, 2);
                break;

            // case 'duration':
            case 'xmpdm:duration':
                $this->duration = (int) ($value / 1000); // FIXME Milliseconds or seconds?
                break;

            case 'xmpdm:releaseDate':
                $this->releaseYear = (int) $value;
                break;

            case 'xmpdm:discnumber':
                $this->discNumber = (int) $value;
                break;

            case 'xmpdm:tracknumber':
                $this->trackNumber = (int) $value;
                break;

            case 'xmpdm:compilation':
                $this->compilation = (bool) $value;
                break;

            case 'creation-date':
            case 'date':
                $value = preg_replace('/\.\d+/', 'Z', $value);
                $this->created = new DateTime($value, $timezone);
                break;

            case 'last-modified':
                $value = preg_replace('/\.\d+/', 'Z', $value);
                $this->updated = new DateTime($value, $timezone);
                break;

            case 'content-type':
                $mime = $value ? preg_split('/;\s+/', $value) : [];
                $this->mime = array_shift($mime);
                break;

            case 'xmp:creatortool':
                $this->generator = trim($value);
                break;

            default:
                return false;
        }

        return true;
    }
}
