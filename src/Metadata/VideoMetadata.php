<?php

namespace Vaites\ApacheTika\Metadata;

/**
 * Metadata class for video files
 *
 * @author  David Martínez <contacto@davidmartinez.net>
 */
class VideoMetadata extends Metadata
{
    /**
     * Video duration in seconds.
     *
     * @var int
     */
    public $duration = 0;

    /**
     * Video width in pixels
     *
     * @var int
     */
    public $width = 0;

    /**
     * Video height in pixels
     *
     * @var int
     */
    public $height = 0;

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
        switch(mb_strtolower($key))
        {
            case 'length':
            case 'height':
                $this->height = (int) $value;
                break;

            case 'width':
                $this->width = (int) $value;
                break;

            case 'duration':
                $this->duration = (int) $value;
                break;

            default:
                return false;
        }

        return true;
    }
}
