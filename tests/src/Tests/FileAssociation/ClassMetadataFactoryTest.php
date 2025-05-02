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
use Rekalogika\File\Association\ClassMetadataFactory\DefaultClassMetadataFactory;
use Rekalogika\File\Association\Model\FetchMode;
use Rekalogika\File\Association\PropertyLister\AttributesPropertyLister;
use Rekalogika\File\Tests\Tests\Model\EntityWithDifferentFileProperties;

final class ClassMetadataFactoryTest extends TestCase
{
    public function testPropertyInspector(): void
    {
        $propertyLister = new AttributesPropertyLister();
        $factory = new DefaultClassMetadataFactory($propertyLister);
        $object = new EntityWithDifferentFileProperties();
        $class = $object::class;

        $result = $factory->getClassMetadata($class)->getProperty('mandatoryEager');
        $this->assertFalse($result->isMandatory());
        $this->assertTrue($result->getFetch() === FetchMode::Eager);

        $result = $factory->getClassMetadata($class)->getProperty('notMandatoryEager');
        $this->assertTrue($result->isMandatory());
        $this->assertTrue($result->getFetch() === FetchMode::Eager);

        $result = $factory->getClassMetadata($class)->getProperty('mandatoryLazy');
        $this->assertFalse($result->isMandatory());
        $this->assertTrue($result->getFetch() === FetchMode::Lazy);

        $result = $factory->getClassMetadata($class)->getProperty('notMandatoryLazy');
        $this->assertTrue($result->isMandatory());
        $this->assertTrue($result->getFetch() === FetchMode::Lazy);
    }
}
