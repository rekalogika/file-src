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
 * The result of a property inspection.
 */
final readonly class PropertyMetadata
{
    /**
     * @param class-string $class
     */
    public function __construct(
        private string $name,
        private string $class,
        private bool $mandatory,
        private FetchMode $fetch,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return class-string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    public function isMandatory(): bool
    {
        return $this->mandatory;
    }

    public function getFetch(): FetchMode
    {
        return $this->fetch;
    }
}
