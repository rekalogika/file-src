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

use Rekalogika\File\Association\Exception\PropertyWriter\PropertyWriterException;

/**
 * Writes a valuo to a property of an object..
 */
interface PropertyWriterInterface
{
    /**
     * @throws PropertyWriterException
     */
    public function write(
        object $object,
        string $propertyName,
        mixed $value,
    ): void;
}
