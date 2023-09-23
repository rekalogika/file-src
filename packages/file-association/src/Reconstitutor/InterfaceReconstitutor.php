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
        private FileAssociationManager $fileAssociationService
    ) {
    }

    public static function getClass(): string
    {
        return FileAssociationInterface::class;
    }

    public function onSave(object $object): void
    {
        $this->fileAssociationService->save($object);
    }

    public function onRemove(object $object): void
    {
        $this->fileAssociationService->remove($object);
    }

    public function onLoad(object $object): void
    {
        $this->fileAssociationService->load($object);
    }

    public function onCreate(object $object): void
    {
    }
}
