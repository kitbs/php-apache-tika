<?php

namespace Vaites\ApacheTika\Metadata\Mixins;

trait Compression
{
    protected function addKeysForCompression()
    {
        return [
            'lossless' => [
                'compression',
                'compression lossless'
            ],
        ];
    }

    protected function setLosslessMeta($value)
    {
        $this->meta['lossless'] = ($value == 'true' || strtolower($value) == 'uncompressed');
    }
}
