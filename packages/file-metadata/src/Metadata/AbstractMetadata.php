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

namespace Rekalogika\Domain\File\Metadata\Metadata;

use Rekalogika\Contracts\File\RawMetadataInterface;

abstract class AbstractMetadata
{
    abstract public static function create(
        RawMetadataInterface $metadata
    ): ?static;
}
