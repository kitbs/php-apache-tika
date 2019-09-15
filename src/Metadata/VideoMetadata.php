<?php

namespace Vaites\ApacheTika\Metadata;

use Vaites\ApacheTika\Metadata\Mixins\Duration;
use Vaites\ApacheTika\Metadata\Mixins\Dimensions;
use Vaites\ApacheTika\Metadata\Mixins\Compression;

/**
 * Metadata class for video files
 *
 * @author  David Martínez <contacto@davidmartinez.net>
 */
class VideoMetadata extends Metadata
{
    use Dimensions;
    use Compression;
    use Duration;
}
