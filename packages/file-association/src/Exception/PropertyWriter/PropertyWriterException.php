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

namespace Rekalogika\File\Association\Exception\PropertyWriter;

use Rekalogika\File\Association\Exception\FileAssociationException;

final class PropertyWriterException extends FileAssociationException
{
    /**
     * @param object|array<array-key,mixed> $object
     */
    public function __construct(
        object|array $object,
        string $property,
        mixed $value,
        ?\Throwable $previous = null,
    ) {
        $message = \sprintf('Unable to write "%s" to property "%s" in object "%s"', get_debug_type($value), $property, get_debug_type($object));

        parent::__construct($message, 0, $previous);
    }
}
