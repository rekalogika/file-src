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

namespace Rekalogika\File\Bundle\Debug;

use Rekalogika\Contracts\File\FilePointerInterface;

final readonly class FileOperation
{
    /**
     * @param class-string $class
     * @param class-string $scopeClass
     */
    public function __construct(
        private string $class,
        private string $scopeClass,
        private string $property,
        private string $objectId,
        private FilePointerInterface $filePointer,
    ) {}

    /**
     * @return class-string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @return class-string
     */
    public function getScopeClass(): string
    {
        return $this->scopeClass;
    }

    public function getProperty(): string
    {
        return $this->property;
    }

    public function getObjectId(): string
    {
        return $this->objectId;
    }

    public function getFilePointer(): FilePointerInterface
    {
        return $this->filePointer;
    }
}
