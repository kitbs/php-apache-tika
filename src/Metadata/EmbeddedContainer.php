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
     * Parse Apache Tika response and return a metadata handler.
     *
     * @param   string  $response
     * @param   string  $file
     * @return  \Vaites\ApacheTika\Metadata\Metadata
     * @throws  \Exception
     */
    public static function make(string $response, string $file)
    {
        $response = static::parse($response);
        $embedded = [];

        $container = array_shift($response);

        foreach ($response as $meta) {
            $mime = static::asContentType($meta['Content-Type']);

            $handler = static::getHandler($mime);

            $embedded[] = new $handler($meta, $file);
        }

        $mime = static::asContentType($container['Content-Type']);

        $handler = static::getHandler($mime);

        return new $handler($meta, $file, $embedded);
    }

    public function embedded()
    {
        return $this->embedded;
    }
}
