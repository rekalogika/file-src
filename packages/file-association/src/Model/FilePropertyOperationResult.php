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

final readonly class FilePropertyOperationResult
{
    /**
     * @param class-string $class
     * @param class-string $scopeClass
     */
    public function __construct(
        public FilePropertyOperationType $type,
        public FilePropertyOperationAction $action,
        public string $class,
        public string $scopeClass,
        public string $property,
        public string $objectId,
        public FilePointerInterface $filePointer,
    ) {}

    public function getDescription(): string
    {
        $type = $this->type->toString();
        $action = $this->action->toString();

        return \sprintf(
            'File operation "%s" with result action "%s"',
            $type,
            $action,
        );
    }
}
