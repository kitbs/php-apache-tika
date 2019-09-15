<?php

namespace Vaites\ApacheTika\Metadata\Mixins;

trait Duration
{
    protected function addKeysForDuration()
    {
        return [
            'duration' => [
                'xmpdm:duration',
            ],
        ];
    }

    protected function addCastsForDuration()
    {
        return [
            'duration' => 'int',
        ];
    }
}
