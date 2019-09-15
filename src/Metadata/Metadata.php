<?php

namespace Vaites\ApacheTika\Metadata;

use DateTime;
use Exception;
use DateTimeZone;
use JsonSerializable;

/**
 * Standarized metadata class with common attributes for all document types
 *
 * @author  David Martínez <contacto@davidmartinez.net>
 */
abstract class Metadata implements JsonSerializable
{
    /**
     * The cleaned metadata.
     *
     * @var array
     */
    protected $meta = [];

    /**
     * The raw attributes returned by Apache Tika.
     *
     * @var array
     */
    protected $raw = [];

    /**
     * Specific metadata type handlers.
     *
     * @var array
     */
    protected static $handlers = [
        'image/*' => ImageMetadata::class,
        'audio/*' => AudioMetadata::class,
        'video/*' => VideoMetadata::class,
    ];

    /**
     * The metadata keys that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created' => 'datetime',
        'updated' => 'datetime',
    ];

    /**
     * The standardized metadata keys.
     *
     * @var array
     */
    protected $keys = [
        'title' => [
            'dc:title',
        ],
        'comments',
        'language',
        'keywords' => [
            'keyword',
        ],
        'author' => [
            'author',
            'meta:author',
            'initial-creator',
            'creator',
            'dc:creator',
        ],
        'generator' => [
            'xmp:creatortool',
            'application-name',
            'generator',
            'producer',
        ],
        'content' => [
            'x-tika:content',
        ],
        'created' => [
            'creation-date',
            'date',
            'meta:creation-date',
            'dcterms:created',
        ],
        'updated' => [
            'modified',
            'last-modified',
            'last-save-date',
            'meta:save-date',
            'dcterms:modified',
        ],
        'parser' => [
            'x-parsed-by',
        ],
    ];

    /**
     * Fill metadata from an array.
     *
     * @param   array  $meta
     * @param   string  $file
     * @param   string  $mime
     * @throws \Exception
     */
    public function __construct(array $meta, string $file, string $mime)
    {
        $this->prepareKeys();
        $this->bootMixins();

        $this->fill($meta);

        $this->force('file_type', $mime);

        if (!$this->has('title') && !is_null($file)) {
            $this->set('title', preg_replace('/\..+$/', '', basename($file)));
        }

        if (!$this->has('updated')) {
            $this->set('updated', $this->created);
        }
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
        if (empty($response) || trim($response) == '') {
            throw new Exception('Empty response');
        }

        $meta = json_decode($response, true);

        // $meta = is_array($json) ? current($json) : $json; //TODO Why might this be an array?

        if (json_last_error()) {
            $message = function_exists('json_last_error_msg') ? json_last_error_msg() : 'Error parsing JSON response';

            throw new Exception($message, json_last_error());
        }

        $mime = is_array($meta['Content-Type']) ? current($meta['Content-Type']) : $meta['Content-Type'];

        $handler = static::getHandler($mime);

        return new $handler($meta, $file, $mime);
    }

    /**
     * Get the metadata handler for a mime type.
     * @param  string $mime
     * @return string
     */
    protected static function getHandler(string $mime)
    {
        foreach (static::$handlers as $pattern => $handler) {
            if (preg_match('#^'.str_replace('/*', '/.+', $pattern).'$#', $mime)) {
                return $handler;
            }
        }

        return DocumentMetadata::class;
    }

    /**
     * Register a metadata handler for a mime type.
     * @param  string  $mime
     * @param  string  $handler
     * @param  bool    $prepend
     * @return void
     */
    public static function registerHandler(string $mime, string $handler, bool $prepend = false)
    {
        if ($prepend) {
            static::$handlers = [$mime => $handler] + static::$handlers;
        }
        else {
            static::$handlers = static::$handlers + [$mime => $handler];
        }
    }

    /**
     * Fill the instance with an array of metadata.
     *
     * @param  array  $meta
     * @return void
     */
    protected function fill(array $meta)
    {
        $this->raw = $meta;

        foreach ($meta as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * Get all of the current metadata on the instance.
     *
     * @return array
     */
    public function all()
    {
        return $this->meta + ['_raw' => $this->raw];
    }

    /**
     * Determine if a metadata key exists on the instance.
     *
     * @param  string  $key
     * @return bool
     */
    public function has(string $key)
    {
        return ! is_null($this->get($key));
    }

    /**
     * Get a metadata key from the instance.
     *
     * @param  string  $key
     * @return mixed
     */
    public function get(string $key)
    {
        if (array_key_exists($key, $this->meta)) {
            return $this->meta[$key];
        }
    }

    /**
     * Set a given metadata key on the instance.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    protected function set(string $key, $value)
    {
        if ($key = $this->key($key)) {
            if (!$this->has($key)) {
                if ($method = $this->mutator($key)) {
                    return $this->{$method}($value);
                }

                $this->meta[$key] = $this->cast($key, $value);
            }
        }
    }

    /**
     * Force a given metadata key on the instance.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    protected function force(string $key, $value)
    {
        $this->meta[$key] = $value;
    }

    /**
     * Prepare the standardized metadata keys.
     *
     * @return void
     */
    protected function prepareKeys()
    {
        $oldKeys = $this->keys;
        $keys = [];

        foreach ($oldKeys as $key => $variants) {

            if (is_numeric($key) && is_string($variants)) {
                $keys[$variants] = $variants;
            }
            elseif (is_string($key) && is_string($variants)) {
                $keys[$variants] = $key;
            }
            elseif (is_string($key) && is_array($variants)) {
                foreach ($variants as $variant) {
                    $keys[$variant] = $key;
                }
            }
            else {
                $unexpected = json_encode([$key => $variants]);
                throw new Exception("Unexpected keys: $unexpected");
            }
        }

        $this->keys = $keys;
    }

    /**
     * Boot the keys and casts from all mixin traits.
     *
     * @return void
     */
    protected function bootMixins()
    {
        foreach (get_class_methods($this) as $method) {
            if (stripos($method, 'addKeysFor') !== false) {
                $this->addKeys($this->$method());
            }
            elseif (stripos($method, 'addCastsFor') !== false) {
                $this->addCasts($this->$method());
            }
        }
    }

    /**
     * Get a standardized metadata key.
     *
     * @param  string $key
     * @return string|bool
     */
    protected function key(string $key)
    {
        $key = mb_strtolower($key);

        return $this->keys[$key] ?? false;
    }

    /**
     * Add an array of standardized meta keys.
     *
     * @param array[] $keys
     * @return void
     */
    protected function addKeys(array $keys)
    {
        foreach ($keys as $key => $variants) {
            if (is_numeric($key) && is_string($variants)) {
                $key = $variants;
            }

            foreach ((array) $variants as $variant) {
                $this->keys[$variant] = $key;
            }
        }
    }

    /**
     * Add an array of castable meta keys.
     *
     * @param string[] $casts
     * @return void
     */
    protected function addCasts(array $casts)
    {
        foreach ($casts as $key => $cast) {
            $this->casts[$key] = $cast;
        }
    }

    /**
     * Retrieve the set mutator for a metadata key if it exists.
     *
     * @param  string  $key
     * @return string|null
     */
    protected function mutator(string $key)
    {
        $key = ucwords(preg_replace('/[^A-Za-z0-9]/', ' ', $key));
        $key = str_replace(' ', '', $key);
        $key = 'set'.$key.'Meta';

        return method_exists($this, $key) ? $key : null;
    }

    /**
     * Cast an attribute to a native PHP type.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function cast(string $key, $value)
    {
        if (is_null($value)) {
            return $value;
        }

        if (isset($this->casts[$key])) {
            switch ($this->casts[$key]) {
                case 'int':
                case 'integer':
                    return (int) $value;
                case 'real':
                case 'float':
                case 'double':
                    return (float) $value;
                case 'string':
                    return (string) $value;
                case 'bool':
                case 'boolean':
                    return (bool) $value;
                case 'date':
                case 'datetime':
                    $value = preg_replace('/\.\d+/', 'Z', $value);
                    return new DateTime($value, new DateTimeZone('UTC'));
            }
        }

        return $value;
    }

    /**
     * Convert the metadata instance to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Convert the metadata instance to JSON.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson(int $options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * Convert the metadata into a JSON serializable array.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->all();
    }

    /**
     * Dynamically retrieve metadata keys from the instance.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->get($key);
    }

    /**
     * Determine if an metadata key exists on the instance.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset(string $key)
    {
        return $this->has($key);
    }

    /**
     * Set the language meta key.
     *
     * @param string  $value
     */
    protected function setLanguageMeta(string $value)
    {
        $this->meta['language'] = substr($value, 0, 2);
    }
}
