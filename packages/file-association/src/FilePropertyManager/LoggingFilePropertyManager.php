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

use Psr\Log\LoggerInterface;
use Rekalogika\File\Association\Contracts\FilePropertyManagerInterface;
use Rekalogika\File\Association\Model\PropertyMetadata;
use Rekalogika\File\Association\Model\PropertyOperationAction;
use Rekalogika\File\Association\Model\PropertyOperationResult;

final readonly class LoggingFilePropertyManager implements FilePropertyManagerInterface
{
    public function __construct(
        private FilePropertyManagerInterface $decorated,
        private ?LoggerInterface $logger,
    ) {}

    #[\Override]
    public function flushProperty(
        PropertyMetadata $propertyMetadata,
        object $object,
        string $id,
    ): PropertyOperationResult {
        $result = $this->decorated->flushProperty($propertyMetadata, $object, $id);

        $this->log($result);

        return $result;
    }

    #[\Override]
    public function removeProperty(
        PropertyMetadata $propertyMetadata,
        object $object,
        string $id,
    ): PropertyOperationResult {
        $result = $this->decorated->removeProperty($propertyMetadata, $object, $id);

        $this->log($result);

        return $result;
    }

    #[\Override]
    public function loadProperty(
        PropertyMetadata $propertyMetadata,
        object $object,
        string $id,
    ): PropertyOperationResult {
        $result = $this->decorated->loadProperty($propertyMetadata, $object, $id);

        $this->log($result);

        return $result;
    }

    private function log(PropertyOperationResult $result): void
    {
        if ($result->action === PropertyOperationAction::Nothing) {
            return;
        }

        $context = [
            'type' => $result->type->getString(),
            'action' => $result->action->getString(),
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
