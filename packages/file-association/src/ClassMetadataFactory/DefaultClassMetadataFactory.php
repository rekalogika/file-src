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
use Rekalogika\File\Association\Model\ClassMetadata;
use Rekalogika\File\Association\Model\FetchMode;
use Rekalogika\File\Association\Model\Property;
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
        $properties = $this->propertyLister->getFileProperties($class);

        $propertyMetadatas = [];

        foreach ($properties as $property) {
            $propertyName = $property->getName();

            $propertyMetadatas[$propertyName] = $this->createPropertyMetadata(
                leafClass: $class,
                property: $property,
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
     * @param class-string $leafClass
     */
    private function createPropertyMetadata(
        string $leafClass,
        Property $property,
    ): PropertyMetadata {
        $class = $property->getClass();
        $propertyName = $property->getName();

        $reflectionClass = new \ReflectionClass($class);
        $reflectionProperty = $reflectionClass->getProperty($propertyName);
        $reflectionType = $reflectionProperty->getType();
        $mandatory = !($reflectionType?->allowsNull() ?? true);

        $attributes = $reflectionProperty
            ->getAttributes(AsFileAssociation::class);

        if ($attributes === []) {
            return new PropertyMetadata(
                class: $leafClass,
                scopeClass: $class,
                name: $propertyName,
                mandatory: $mandatory,
                fetch: FetchMode::Eager,
            );
        }

        $attribute = $attributes[0]->newInstance();

        return new PropertyMetadata(
            class: $leafClass,
            scopeClass: $class,
            name: $propertyName,
            mandatory: $mandatory,
            fetch: $attribute->fetch,
        );
    }
}
