<?php

namespace Vaites\ApacheTika\Metadata;

use Vaites\ApacheTika\Metadata\Mixins\Dimensions;
use Vaites\ApacheTika\Metadata\Mixins\Compression;

/**
 * Metadata class for images
 *
 * @author  David Martínez <contacto@davidmartinez.net>
 */
class ImageMetadata extends Metadata
{
    use Dimensions;
    use Compression;
}
