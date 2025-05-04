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

use Rekalogika\Contracts\File\FilePointerInterface;

final readonly class PropertyOperationResult
{
    /**
     * @param class-string $class
     * @param class-string $scopeClass
     */
    public function __construct(
        public ObjectOperationType $type,
        public PropertyOperationAction $action,
        public string $class,
        public string $scopeClass,
        public string $property,
        public string $objectId,
        public FilePointerInterface $filePointer,
        public ?float $duration = null,
    ) {}

    public function withDuration(float $duration): self
    {
        return new self(
            type: $this->type,
            action: $this->action,
            class: $this->class,
            scopeClass: $this->scopeClass,
            property: $this->property,
            objectId: $this->objectId,
            filePointer: $this->filePointer,
            duration: $duration,
        );
    }
}
