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

final readonly class ObjectOperationResult
{
    /**
     * @param class-string $class
     * @param list<PropertyOperationResult> $propertyResults
     */
    public function __construct(
        public ObjectOperationType $type,
        public string $class,
        public string $objectId,
        public array $propertyResults,
        public ?float $duration = null,
    ) {}

    public function withDuration(float $duration): self
    {
        return new self(
            type: $this->type,
            class: $this->class,
            objectId: $this->objectId,
            propertyResults: $this->propertyResults,
            duration: $duration,
        );
    }
}
