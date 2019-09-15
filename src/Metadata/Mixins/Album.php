<?php

namespace Vaites\ApacheTika\Metadata\Mixins;

trait Album
{
    protected function addKeysForAlbum()
    {
        return [
            'artist' => [
                'xmpdm:artist',
            ],
            'album' => [
                'xmpdm:album',
            ],
            'album_artist' => [
                'xmpdm:albumartist',
            ],
            'disc' => [
                'xmpdm:discnumber',
            ],
            'track' => [
                'xmpdm:tracknumber',
            ],
            'compilation' => [
                'xmpdm:compilation',
            ],
            'composer' => [
                'xmpdm:composer',
            ],
            'released' => [
                'xmpdm:releasedate',
            ],
            'comments' => [
                'xmpDM:logComment',
            ],
            'genre' => [
                'xmpDM:genre',
            ],
        ];
    }

    protected function addCastsForAlbum()
    {
        return [
            'disc'        => 'int',
            'track'       => 'int',
            'released'    => 'int',
            'compilation' => 'bool',
        ];
    }

    protected function setArtistMeta($value)
    {
        if (stripos($value, '/') !== false) {
            $value = explode('/', $value);
        }

        $this->meta['artist'] = $value;
    }

    protected function setAlbumArtistMeta($value)
    {
        if (stripos($value, '/') !== false) {
            $value = explode('/', $value);
        }

        $this->meta['album_artist'] = $value;
    }
}
