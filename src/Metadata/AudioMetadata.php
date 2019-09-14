<?php

namespace Vaites\ApacheTika\Metadata;

/**
 * Metadata class for audio files
 *
 * @author  David Martínez <contacto@davidmartinez.net>
 */
class AudioMetadata extends Metadata
{
    /**
     * Audio duration in seconds.
     *
     * @var int
     */
    public $duration = 0;

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
            case 'title':
                $this->title = $value;
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
