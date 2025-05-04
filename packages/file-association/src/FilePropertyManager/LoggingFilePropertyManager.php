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

use Rekalogika\File\Association\Contracts\ClassBasedFileLocationResolverInterface;
use Rekalogika\File\Association\Contracts\FilePropertyManagerInterface;
use Rekalogika\File\Association\Model\FilePropertyOperationResult;
use Rekalogika\File\Association\Model\FilePropertyOperationType;
use Rekalogika\File\Association\Model\PropertyMetadata;
use Symfony\Component\Stopwatch\Stopwatch;

final readonly class TraceableFilePropertyManager implements FilePropertyManagerInterface
{
    public function __construct(
        private FilePropertyManagerInterface $decorated,
        private Stopwatch $stopwatch,
        private FileDataCollector $dataCollector,
    ) {}

    #[\Override]
    public function flushProperty(
        PropertyMetadata $propertyMetadata,
        object $object,
        string $id,
    ): FilePropertyOperationResult {
        $this->stopwatch->start('file.property_manager.flush');
        $result = $this->decorated->flushProperty($propertyMetadata, $object, $id);
        $this->stopwatch->stop('file.property_manager.flush');

        $this->dataCollector->collectFilePropertyOperationResult($result);

        $this->log($result);

        return $result;
    }

    #[\Override]
    public function removeProperty(
        PropertyMetadata $propertyMetadata,
        object $object,
        string $id,
    ): FilePropertyOperationResult {
        $this->stopwatch->start('file.property_manager.remove');
        $result = $this->decorated->removeProperty($propertyMetadata, $object, $id);
        $this->stopwatch->stop('file.property_manager.remove');

        $this->dataCollector->collectFilePropertyOperationResult($result);

        $this->log($result);

        return $result;
    }

    #[\Override]
    public function loadProperty(
        PropertyMetadata $propertyMetadata,
        object $object,
        string $id,
    ): FilePropertyOperationResult {
        $this->stopwatch->start('file.property_manager.load');
        $result = $this->decorated->loadProperty($propertyMetadata, $object, $id);
        $this->stopwatch->stop('file.property_manager.load');

        $this->dataCollector->collectFilePropertyOperationResult($result);

        $this->log($result);

        return $result;
    }

    private function log(FilePropertyOperationResult $result): void
    {
        if ($result->action === FilePropertyOperationAction::Nothing) {
            return;
        }

        $context = [
            'type' => $result->type->toString(),
            'action' => $result->action->toString(),
            'class' => $result->class,
            'property' => $result->property,
            'scopeClass' => $result->scopeClass,
            'objectId' => $result->objectId,
            'fileKey' => $result->filePointer->getKey(),
            'fileFilesystemIdentifier' => $result->filePointer->getFilesystemIdentifier(),
        ];

        $this->logger?->debug(
            'File operation "{type}", with result action "{action}"',
            $context,
        );
    }
}
