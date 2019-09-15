<?php

namespace Vaites\ApacheTika\Metadata\Mixins;

trait Document
{
    protected function addKeysForDocument()
    {
        return [
            'keywords' => [
                'keyword',
            ],
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

    protected function setKeywordsMeta($value)
    {
        $keywords = preg_split(preg_match('/,/', $value) ? '/\s*,\s*/' : '/\s+/', $value);
        $this->meta['keywords'] = array_unique($keywords ?: []);
    }
}
