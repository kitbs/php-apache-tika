<?php

namespace Vaites\ApacheTika\Metadata;

/**
 * Metadata class for files containing embedded files.
 *
 * @author  David Martínez <contacto@davidmartinez.net>
 */
class EmbeddedContainer extends Metadata
{
    /**
     * The embedded files.
     *
     * @var array[]
     */
    protected $embedded = [];

    /**
     * Fill metadata from an array.
     *
     * @param   array  $meta
     * @param   string  $file
     * @throws \Exception
     */
    public function __construct(array $embedded, string $file)
    {
        $this->embedded = $embedded;

        parent::__construct([], $file);
    }

    /**
     * Parse Apache Tika response and return a metadata handler.
     *
     * @param   string  $response
     * @param   string  $file
     * @return  \Vaites\ApacheTika\Metadata\Metadata
     * @throws  \Exception
     */
    public static function make(string $response, string $file)
    {
        $metas = static::parse($response);
        $embedded = [];

        foreach ($metas as $index => $meta) {
            $mime = static::asContentType($meta['Content-Type']);
            $embeddedFile = $meta['resourceName'] ?? $file;
            $index = $index > 0 ? ($meta['X-TIKA:embedded_resource_path'] ?? '#'.$index) : '/';

            $handler = static::getHandler($mime);

            $embedded[$index] = new $handler($meta, $embeddedFile);
        }

        return new static($embedded, $file);
    }

    public function embedded()
    {
        return $this->embedded;
    }
}
