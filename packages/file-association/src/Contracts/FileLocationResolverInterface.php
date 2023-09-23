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

use Rekalogika\Contracts\File\FilePointerInterface;
use Rekalogika\File\Association\Exception\FileLocationResolver\FileLocationResolverException;

/**
 * Determines where a file is stored depending on the object and
 * property name.
 *
 * @throws FileLocationResolverException
 */
interface FileLocationResolverInterface
{
    public function getFileLocation(
        object $object,
        string $propertyName
    ): FilePointerInterface;
}
