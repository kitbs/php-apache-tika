<?php

namespace Vaites\ApacheTika\Metadata\Mixins;

trait Dimensions
{
    protected function addKeysForDimensions()
    {
        return [
            'height' => [
                'image height',
                'tiff:imageheight',
                'tiff:imagelength',
            ],
            'width' => [
                'image width',
                'tiff:imagewidth',
            ],
        ];
    }

    protected function addCastsForDimensions()
    {
        return [
            'height' => 'int',
            'width'  => 'int',
        ];
    }
}
