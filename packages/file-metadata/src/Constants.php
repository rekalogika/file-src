<?php

declare(strict_types=1);

/*
 * This file is part of rekalogika/file-src package.
 *
 * (c) Priyadi Iman Nurcahyo <https://rekalogika.dev>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Rekalogika\Domain\File\Metadata;

/**
 * List of all the supported metadata tags
 *
 * @internal
 */
class Constants
{
    public const HTTP_CACHE_CONTROL = 'http.cacheControl';

    public const HTTP_DISPOSITION = 'http._disposition';

    public const HTTP_ETAG = 'http.eTag';

    public const FILE_NAME = 'file.name';

    public const FILE_SIZE = 'file.size';

    public const FILE_TYPE = 'file.type';

    public const FILE_MODIFICATION_TIME = 'file.modificationTime';

    public const MEDIA_WIDTH = 'media.width';

    public const MEDIA_HEIGHT = 'media.height';
}
