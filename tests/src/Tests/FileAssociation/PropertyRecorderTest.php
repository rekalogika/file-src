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
use Rekalogika\File\Association\PropertyRecorder\PropertyRecorder;

final class PropertyRecorderTest extends TestCase
{
    private PropertyRecorder $propertyRecorder;

    #[\Override]
    protected function setUp(): void
    {
        $this->propertyRecorder = new PropertyRecorder();
    }

    public function testSaveInitialProperty(): void
    {
        $object = new \stdClass();
        $propertyName = 'testProperty';
        $value = 'testValue';

        $this->propertyRecorder->saveInitialProperty($object, $propertyName, $value);

        $this->assertTrue($this->propertyRecorder->hasInitialProperty($object, $propertyName));
        $this->assertSame($value, $this->propertyRecorder->getInitialProperty($object, $propertyName));
    }

    public function testGetInitialProperty(): void
    {
        $object = new \stdClass();
        $propertyName = 'testProperty';
        $value = 'testValue';

        $this->propertyRecorder->saveInitialProperty($object, $propertyName, $value);

        $this->assertSame($value, $this->propertyRecorder->getInitialProperty($object, $propertyName));
        $this->assertNull($this->propertyRecorder->getInitialProperty(new \stdClass(), 'nonExistentProperty'));
    }

    public function testHasInitialProperty(): void
    {
        $object = new \stdClass();
        $propertyName = 'testProperty';

        $this->assertFalse($this->propertyRecorder->hasInitialProperty($object, $propertyName));

        $this->propertyRecorder->saveInitialProperty($object, $propertyName, 'testValue');

        $this->assertTrue($this->propertyRecorder->hasInitialProperty($object, $propertyName));
    }

    public function testRemoveInitialProperty(): void
    {
        $object = new \stdClass();
        $propertyName = 'testProperty';
        $value = 'testValue';

        $this->propertyRecorder->saveInitialProperty($object, $propertyName, $value);
        $this->assertTrue($this->propertyRecorder->hasInitialProperty($object, $propertyName));

        $this->propertyRecorder->removeInitialProperty($object, $propertyName);
        $this->assertFalse($this->propertyRecorder->hasInitialProperty($object, $propertyName));
    }

    public function testReset(): void
    {
        $object = new \stdClass();
        $propertyName = 'testProperty';
        $value = 'testValue';

        $this->propertyRecorder->saveInitialProperty($object, $propertyName, $value);
        $this->assertTrue($this->propertyRecorder->hasInitialProperty($object, $propertyName));

        $this->propertyRecorder->reset();
        $this->assertFalse($this->propertyRecorder->hasInitialProperty($object, $propertyName));
    }
}
