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

use Rekalogika\File\Association\Attribute\WithFileAssociation;
use Rekalogika\File\Association\FileAssociationManager;
use Rekalogika\Reconstitutor\Contract\AttributeReconstitutorInterface;

/**
 * Reconstitutes objects with WithFileAssociation attribute
 */
class AttributeReconstitutor implements AttributeReconstitutorInterface
{
    public function __construct(
        private FileAssociationManager $fileAssociationManager
    ) {
    }

    public static function getAttributeClass(): string
    {
        return WithFileAssociation::class;
    }

    public function onSave(object $object): void
    {
        $this->fileAssociationManager->save($object);
    }

    public function onRemove(object $object): void
    {
        $this->fileAssociationManager->remove($object);
    }

    public function onLoad(object $object): void
    {
        $this->fileAssociationManager->load($object);
    }

    public function onCreate(object $object): void
    {
    }
}
