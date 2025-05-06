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
use Rekalogika\File\Association\Contracts\ObjectManagerInterface;
use Rekalogika\Reconstitutor\Contract\ClassReconstitutorInterface;

/**
 * Reconstitutes objects that implement FileAssociationInterface.
 *
 * @implements ClassReconstitutorInterface<FileAssociationInterface>
 */
final readonly class InterfaceReconstitutor implements ClassReconstitutorInterface
{
    public function __construct(
        private ObjectManagerInterface $objectManager,
    ) {}

    #[\Override]
    public static function getClass(): string
    {
        return FileAssociationInterface::class;
    }

    #[\Override]
    public function onSave(object $object): void
    {
        $this->objectManager->flushObject($object);
    }

    #[\Override]
    public function onRemove(object $object): void
    {
        $this->objectManager->removeObject($object);
    }

    #[\Override]
    public function onLoad(object $object): void
    {
        $this->objectManager->loadObject($object);
    }

    #[\Override]
    public function onCreate(object $object): void {}

    #[\Override]
    public function onClear(object $object): void {}
}
