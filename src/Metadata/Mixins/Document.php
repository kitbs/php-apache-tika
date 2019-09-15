<?php

namespace Vaites\ApacheTika\Metadata\Mixins;

trait Document
{
    protected function addKeysForDocument()
    {
        return [
            'pages' => [
                'nbpage',
                'page-count',
                'xmptpg:npages',
            ],
            'words' => [
                'nbword',
                'word-count',
            ],
            'encoding' => [
                'content-encoding',
            ],
        ];
    }

    protected function addCastsForDocument()
    {
        return [
            'pages' => 'int',
            'words' => 'int',
        ];
    }
}
