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
            'release_date' => [
                'xmpdm:releasedate',
            ],
        ];
    }

    protected function addCastsForAlbum()
    {
        return [
            'disc'        => 'int',
            'track'       => 'int',
            'compilation' => 'bool',
        ];
    }
}
