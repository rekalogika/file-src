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

use Rekalogika\File\Association\Attribute\AsFileAssociation;
use Rekalogika\File\Association\Contracts\PropertyListerInterface;
use Rekalogika\File\Association\Model\Property;
use Rekalogika\File\Association\Util\ClassUtil;

/**
 * Determines all the file association properties by looking at
 * AsFileAssociation attributes.
 */
final readonly class AttributesPropertyLister implements PropertyListerInterface
{
    #[\Override]
    public function getFileProperties(string $class): iterable
    {
        $reflectionProperties = ClassUtil::getReflectionPropertiesWithAttribute(
            class: $class,
            attribute: AsFileAssociation::class,
        );

        foreach ($reflectionProperties as $reflectionProperty) {
            yield new Property(
                class: $reflectionProperty->getDeclaringClass()->getName(),
                name: $reflectionProperty->getName(),
            );
        }
    }
}
