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

namespace Rekalogika\File\Association\Exception\PropertyReader;

use Rekalogika\File\Association\Exception\FileAssociationException;

class PropertyReaderException extends FileAssociationException
{
    public function __construct(
        object $object,
        string $property,
        \Throwable $previous = null
    ) {
        $message = sprintf('Unable to read property "%s" in object "%s"', $property, \get_debug_type($object));

        parent::__construct($message, 0, $previous);
    }
}
