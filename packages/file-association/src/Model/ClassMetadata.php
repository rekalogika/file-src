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
     * @param class-string $class
     * @param array<string,PropertyMetadata> $properties
     */
    public function __construct(
        private string $class,
        private array $properties,
    ) {}

    /**
     * @return class-string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @return array<string,PropertyMetadata>
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getProperty(string $name): PropertyMetadata
    {
        return $this->properties[$name] ?? throw new \InvalidArgumentException(\sprintf('Property "%s" not found in class "%s".', $name, $this->class));
    }
}
