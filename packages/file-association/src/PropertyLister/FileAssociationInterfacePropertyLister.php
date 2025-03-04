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

namespace Rekalogika\File\Association\PropertyLister;

use Rekalogika\Contracts\File\Association\FileAssociationInterface;
use Rekalogika\File\Association\Contracts\PropertyListerInterface;

/**
 * Determines applicable file association properties by using
 * FileAssociationInterface.
 */
final class FileAssociationInterfacePropertyLister implements PropertyListerInterface
{
    #[\Override]
    public function getFileProperties(object $object): iterable
    {
        if (!$object instanceof FileAssociationInterface) {
            return [];
        }

        return $object::getFileAssociationPropertyList();
    }
}
