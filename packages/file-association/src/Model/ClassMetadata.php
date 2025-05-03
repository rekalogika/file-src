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

namespace Rekalogika\File\Association\Model;

/**
 * Contains metadata about a class.
 */
final readonly class ClassMetadata
{
    /**
     * @var array<string,list<PropertyMetadata>>
     */
    private array $propertiesByName;

    /**
     * @var list<PropertyMetadata>
     */
    private array $allProperties;

    /**
     * @param class-string $class
     * @param iterable<PropertyMetadata> $properties
     */
    public function __construct(
        private string $class,
        private string $signature,
        iterable $properties,
    ) {
        $propertiesByName = [];
        $allProperties = [];

        foreach ($properties as $property) {
            $propertiesByName[$property->getName()][] = $property;
            $allProperties[] = $property;
        }

        $this->propertiesByName = $propertiesByName;
        $this->allProperties = $allProperties;
    }

    /**
     * @return class-string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    public function getSignature(): string
    {
        return $this->signature;
    }

    /**
     * @return list<PropertyMetadata>
     */
    public function getProperties(): array
    {
        return $this->allProperties;
    }

    /**
     * @return list<string>
     */
    public function getPropertyNames(): array
    {
        return array_keys($this->propertiesByName);
    }

    /**
     * @return list<PropertyMetadata>
     */
    public function getPropertiesByName(string $name): array
    {
        return $this->propertiesByName[$name]
            ?? throw new \InvalidArgumentException(\sprintf('Property "%s" not found in class "%s".', $name, $this->class));
    }
}
