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

namespace Rekalogika\File\Tests\FileAssociation;

use PHPUnit\Framework\TestCase;
use Rekalogika\File\Association\Exception\PropertyInspector\PropertyMetadataNotFoundException;
use Rekalogika\File\Association\PropertyInspector\PropertyInspector;
use Rekalogika\File\Tests\Model\EntityWithDifferentFileProperties;

class PropertyInspectorTest extends TestCase
{
    public function testPropertyInspector(): void
    {
        $inspector = new PropertyInspector;
        $object = new EntityWithDifferentFileProperties;

        $result = $inspector->inspect($object, 'nullableEager');
        $this->assertTrue($result->isNullable());
        $this->assertTrue($result->getFetch() === 'EAGER');

        $result = $inspector->inspect($object, 'notNullableEager');
        $this->assertFalse($result->isNullable());
        $this->assertTrue($result->getFetch() === 'EAGER');

        $result = $inspector->inspect($object, 'nullableLazy');
        $this->assertTrue($result->isNullable());
        $this->assertTrue($result->getFetch() === 'LAZY');

        $result = $inspector->inspect($object, 'notNullableLazy');
        $this->assertFalse($result->isNullable());
        $this->assertTrue($result->getFetch() === 'LAZY');
    }
}
