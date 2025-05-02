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

namespace Rekalogika\File\Association\ClassMetadataFactory;

use Rekalogika\File\Association\Attribute\AsFileAssociation;
use Rekalogika\File\Association\Contracts\ClassMetadataFactoryInterface;
use Rekalogika\File\Association\Contracts\ClassSignatureResolverInterface;
use Rekalogika\File\Association\Contracts\PropertyListerInterface;
use Rekalogika\File\Association\Exception\PropertyInspector\MissingPropertyException;
use Rekalogika\File\Association\Model\ClassMetadata;
use Rekalogika\File\Association\Model\FetchMode;
use Rekalogika\File\Association\Model\PropertyMetadata;

final readonly class DefaultClassMetadataFactory implements ClassMetadataFactoryInterface
{
    public function __construct(
        private PropertyListerInterface $propertyLister,
        private ClassSignatureResolverInterface $classSignatureResolver,
    ) {}

    #[\Override]
    public function getClassMetadata(string $class): ClassMetadata
    {
        $reflectionClass = new \ReflectionClass($class);
        $mockObject = $reflectionClass->newInstanceWithoutConstructor();

        // @todo refactor property lister to accept class directly
        $properties = $this->propertyLister->getFileProperties($mockObject);

        $propertyMetadatas = [];

        foreach ($properties as $propertyName) {
            $propertyMetadatas[$propertyName] = $this->createPropertyMetadata(
                class: $class,
                propertyName: $propertyName,
            );
        }

        $signature = $this->classSignatureResolver->getClassSignature($class)
            ?? throw new \LogicException(\sprintf(
                'No class signature resolver found for class "%s"',
                $class,
            ));

        return new ClassMetadata(
            class: $class,
            signature: $signature,
            properties: $propertyMetadatas,
        );
    }

    /**
     * @param class-string $class
     */
    private function createPropertyMetadata(
        string $class,
        string $propertyName,
    ): PropertyMetadata {
        $reflectionClass = new \ReflectionClass($class);
        $reflectionProperty = null;

        while ($reflectionClass instanceof \ReflectionClass) {
            if ($reflectionClass->hasProperty($propertyName)) {
                $reflectionProperty = $reflectionClass->getProperty($propertyName);
                break;
            }

            $reflectionClass = $reflectionClass->getParentClass();
        }

        if (!$reflectionProperty instanceof \ReflectionProperty) {
            throw new MissingPropertyException(
                propertyName: $propertyName,
                class: $class,
            );
        }

        $reflectionType = $reflectionProperty->getType();
        $declaringClass = $reflectionProperty->getDeclaringClass()->getName();
        $mandatory = !($reflectionType?->allowsNull() ?? true);

        $attributes = $reflectionProperty
            ->getAttributes(AsFileAssociation::class);

        if ($attributes === []) {
            return new PropertyMetadata(
                name: $propertyName,
                class: $declaringClass,
                mandatory: $mandatory,
                fetch: FetchMode::Eager,
            );
        }

        $attribute = $attributes[0]->newInstance();

        return new PropertyMetadata(
            name: $propertyName,
            class: $declaringClass,
            mandatory: $mandatory,
            fetch: $attribute->fetch,
        );
    }
}
