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

namespace Rekalogika\File\Association\Exception;

final class DuplicatePropertyNameException extends LogicException
{
    /**
     * @param string $propertyName
     * @param class-string $class1
     * @param class-string $class2
     * @param string $leafClass
     */
    public function __construct(
        string $propertyName,
        string $class1,
        string $class2,
        string $leafClass,
    ) {
        parent::__construct(
            \sprintf(
                'Invalid class signature for property "%s" in class "%s" and "%s". Leaf class: "%s"',
                $propertyName,
                $class1,
                $class2,
                $leafClass,
            ),
        );
    }
}
