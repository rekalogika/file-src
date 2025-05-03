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

namespace Rekalogika\File\Association\Contracts;

use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\File\Association\Model\PropertyMetadata;

/**
 * Reads a property of an object.
 *
 * @internal
 */
interface PropertyReaderInterface
{
    public function read(
        object $object,
        PropertyMetadata $propertyMetadata,
    ): ?FileInterface;
}
