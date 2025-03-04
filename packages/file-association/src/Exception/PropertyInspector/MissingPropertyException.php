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

namespace Rekalogika\File\Association\Exception\PropertyInspector;

final class MissingPropertyException extends PropertyInspectorException
{
    public function __construct(
        string $propertyName,
        object $object,
    ) {
        parent::__construct(
            \sprintf(
                'Property "%s" not found in object "%s"',
                $propertyName,
                $object::class,
            ),
        );
    }
}
