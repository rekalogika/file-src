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
use Rekalogika\File\Association\PropertyInspector\PropertyInspector;
use Rekalogika\File\Tests\Model\EntityWithDifferentFileProperties;

class PropertyInspectorTest extends TestCase
{
    public function testPropertyInspector(): void
    {
        $inspector = new PropertyInspector();
        $object = new EntityWithDifferentFileProperties();

        $result = $inspector->inspect($object, 'mandatoryEager');
        $this->assertFalse($result->isMandatory());
        $this->assertTrue($result->getFetch() === 'EAGER');

        $result = $inspector->inspect($object, 'notMandatoryEager');
        $this->assertTrue($result->isMandatory());
        $this->assertTrue($result->getFetch() === 'EAGER');

        $result = $inspector->inspect($object, 'mandatoryLazy');
        $this->assertFalse($result->isMandatory());
        $this->assertTrue($result->getFetch() === 'LAZY');

        $result = $inspector->inspect($object, 'notMandatoryLazy');
        $this->assertTrue($result->isMandatory());
        $this->assertTrue($result->getFetch() === 'LAZY');
    }
}
