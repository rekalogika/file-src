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
use Rekalogika\File\Association\Exception\DuplicatePropertyNameException;
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
        $result = [];

        foreach ($properties as $reflectionProperty) {
            $name = $reflectionProperty->getName();

            if (!\in_array($name, $propertyNames, true)) {
                continue;
            }

            if (isset($result[$name])) {
                $previous = $result[$name];

                throw new DuplicatePropertyNameException(
                    propertyName: $name,
                    class1: $previous->getClass(),
                    class2: $reflectionProperty->getDeclaringClass()->getName(),
                    leafClass: $class,
                );
            }

            $result[$name] = new Property(
                class: $reflectionProperty->getDeclaringClass()->getName(),
                name: $name,
            );
        }

        return array_values($result);
    }
}
