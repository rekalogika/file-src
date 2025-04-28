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
use Rekalogika\File\Association\PropertyInspector\PropertyInspector;
use Rekalogika\File\Tests\Tests\Model\EntityWithDifferentFileProperties;

final class PropertyInspectorTest extends TestCase
{
    public function testPropertyInspector(): void
    {
        $inspector = new PropertyInspector();
        $object = new EntityWithDifferentFileProperties();
        $class = $object::class;

        $result = $inspector->inspect($class, 'mandatoryEager');
        $this->assertFalse($result->isMandatory());
        $this->assertTrue($result->getFetch() === 'EAGER');

        $result = $inspector->inspect($class, 'notMandatoryEager');
        $this->assertTrue($result->isMandatory());
        $this->assertTrue($result->getFetch() === 'EAGER');

        $result = $inspector->inspect($class, 'mandatoryLazy');
        $this->assertFalse($result->isMandatory());
        $this->assertTrue($result->getFetch() === 'LAZY');

        $result = $inspector->inspect($class, 'notMandatoryLazy');
        $this->assertTrue($result->isMandatory());
        $this->assertTrue($result->getFetch() === 'LAZY');
    }
}
