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

namespace Rekalogika\File\Association\FilePropertyManager;

use Rekalogika\File\Association\Contracts\FilePropertyManagerInterface;
use Rekalogika\File\Association\Contracts\FilePropertyOperation;
use Rekalogika\File\Association\Model\PropertyMetadata;

final class TraceableFilePropertyManager implements FilePropertyManagerInterface
{
    public function __construct(
        private readonly FilePropertyManagerInterface $decorated,
    ) {}

    #[\Override]
    public function flushProperty(
        PropertyMetadata $propertyMetadata,
        object $object,
        string $id,
    ): FilePropertyOperation {
        $result = $this->decorated->flushProperty($propertyMetadata, $object, $id);

        return $result;
    }

    #[\Override]
    public function removeProperty(
        PropertyMetadata $propertyMetadata,
        object $object,
        string $id,
    ): FilePropertyOperation {
        $result = $this->decorated->removeProperty($propertyMetadata, $object, $id);

        return $result;
    }

    #[\Override]
    public function loadProperty(
        PropertyMetadata $propertyMetadata,
        object $object,
        string $id,
    ): FilePropertyOperation {
        $result = $this->decorated->loadProperty($propertyMetadata, $object, $id);

        return $result;
    }
}
