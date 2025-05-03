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

namespace Rekalogika\File\Association\PropertyReaderWriter;

use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\File\Association\Contracts\PropertyReaderInterface;
use Rekalogika\File\Association\Contracts\PropertyWriterInterface;
use Rekalogika\File\Association\Model\PropertyMetadata;

final readonly class DefaultPropertyReaderWriter implements
    PropertyReaderInterface,
    PropertyWriterInterface
{
    #[\Override]
    public function read(
        object $object,
        PropertyMetadata $propertyMetadata,
    ): ?FileInterface {
        $reflectionClass = new \ReflectionClass($propertyMetadata->getScopeClass());

        $reflectionProperty = $reflectionClass
            ->getProperty($propertyMetadata->getName());

        $result = $reflectionProperty->getValue($object);

        if ($result === null) {
            return null;
        }

        if (!$result instanceof FileInterface) {
            throw new \UnexpectedValueException(\sprintf(
                'Property "%s" of class "%s" must be an instance of "%s", "%s" given',
                $propertyMetadata->getName(),
                $propertyMetadata->getScopeClass(),
                FileInterface::class,
                get_debug_type($result),
            ));
        }

        return $result;
    }

    #[\Override]
    public function write(
        object $object,
        PropertyMetadata $propertyMetadata,
        ?FileInterface $value,
    ): void {
        $reflectionClass = new \ReflectionClass($propertyMetadata->getScopeClass());

        $reflectionProperty = $reflectionClass
            ->getProperty($propertyMetadata->getName());

        $reflectionProperty->setValue($object, $value);
    }
}
