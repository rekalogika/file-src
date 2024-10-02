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

namespace Rekalogika\File\Tests\Tests\FileAssociation;

use PHPUnit\Framework\TestCase;
use Rekalogika\File\Association\PropertyLister\AttributesPropertyLister;
use Rekalogika\File\Tests\Tests\Model\EntityWithAttribute;
use Rekalogika\File\Tests\Tests\Model\SubclassOfEntityWithAttribute;

class PropertyListerTest extends TestCase
{
    public function testAttributePropertyLister(): void
    {
        $lister = new AttributesPropertyLister();

        $entity = new EntityWithAttribute('id');
        $properties = $lister->getFileProperties($entity);
        $properties = $properties instanceof \Traversable
            ? iterator_to_array($properties)
            : $properties;
        $this->assertSame(['file'], $properties);

        $subclassedEntity = new SubclassOfEntityWithAttribute('id');
        $properties = $lister->getFileProperties($subclassedEntity);
        $properties = $properties instanceof \Traversable
            ? iterator_to_array($properties)
            : $properties;
        $this->assertSame(['anotherFile', 'file'], $properties);
    }
}
