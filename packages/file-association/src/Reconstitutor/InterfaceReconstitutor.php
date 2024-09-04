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

namespace Rekalogika\File\Association\Reconstitutor;

use Rekalogika\Contracts\File\Association\FileAssociationInterface;
use Rekalogika\File\Association\FileAssociationManager;
use Rekalogika\Reconstitutor\Contract\ClassReconstitutorInterface;

/**
 * Reconstitutes objects that implement FileAssociationInterface.
 *
 * @implements ClassReconstitutorInterface<FileAssociationInterface>
 */
class InterfaceReconstitutor implements ClassReconstitutorInterface
{
    public function __construct(
        private readonly FileAssociationManager $fileAssociationService
    ) {
    }

    #[\Override]
    public static function getClass(): string
    {
        return FileAssociationInterface::class;
    }

    #[\Override]
    public function onSave(object $object): void
    {
        $this->fileAssociationService->save($object);
    }

    #[\Override]
    public function onRemove(object $object): void
    {
        $this->fileAssociationService->remove($object);
    }

    #[\Override]
    public function onLoad(object $object): void
    {
        $this->fileAssociationService->load($object);
    }

    #[\Override]
    public function onCreate(object $object): void
    {
    }
}
