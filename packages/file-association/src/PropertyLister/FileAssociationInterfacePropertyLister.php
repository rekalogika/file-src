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

namespace Rekalogika\File\Association\PropertyLister;

use Rekalogika\Contracts\File\Association\FileAssociationInterface;
use Rekalogika\File\Association\Contracts\PropertyListerInterface;
use Rekalogika\File\Association\Model\Property;
use Rekalogika\File\Association\Util\ClassUtil;

/**
 * Determines applicable file association properties by using
 * FileAssociationInterface.
 */
final class FileAssociationInterfacePropertyLister implements PropertyListerInterface
{
    #[\Override]
    public function getFileProperties(string $class): iterable
    {
        if (!is_a($class, FileAssociationInterface::class, true)) {
            return [];
        }

        $propertyNames = $class::getFileAssociationPropertyList();
        $properties = ClassUtil::getReflectionProperties($class);

        foreach ($properties as $reflectionProperty) {
            if (!\in_array($reflectionProperty->getName(), $propertyNames, true)) {
                continue;
            }

            yield new Property(
                class: $reflectionProperty->getDeclaringClass()->getName(),
                name: $reflectionProperty->getName(),
            );
        }
    }
}
