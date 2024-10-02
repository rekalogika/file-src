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
use Rekalogika\File\Association\Exception\ObjectIdResolver\EmptyIdException;
use Rekalogika\File\Association\Exception\ObjectIdResolver\IdNotSupportedException;
use Rekalogika\File\Association\Exception\ObjectIdResolver\MethodNotFoundException;
use Rekalogika\File\Association\ObjectIdResolver\DefaultObjectIdResolver;
use Rekalogika\File\Tests\Tests\Model\EntityWithAnyId;
use Rekalogika\File\Tests\Tests\Model\EntityWithoutId;

class DefaultObjectIdResolverTest extends TestCase
{
    public function testIntegerIdDefaultObjectIdResolver(): void
    {
        $objectIdResolver = new DefaultObjectIdResolver();
        $object = new EntityWithAnyId(123);
        $id = $objectIdResolver->getObjectId($object);
        $this->assertSame('123', $id);
    }

    public function testStringIdDefaultObjectIdResolver(): void
    {
        $objectIdResolver = new DefaultObjectIdResolver();
        $object = new EntityWithAnyId('abc');
        $id = $objectIdResolver->getObjectId($object);
        $this->assertSame('abc', $id);
    }

    public function testNullIdDefaultObjectIdResolver(): void
    {
        $objectIdResolver = new DefaultObjectIdResolver();
        $object = new EntityWithAnyId(null);
        $this->expectException(IdNotSupportedException::class);
        $id = $objectIdResolver->getObjectId($object);
    }

    public function testEmptyStringDefaultObjectIdResolver(): void
    {
        $objectIdResolver = new DefaultObjectIdResolver();
        $object = new EntityWithAnyId('');
        $this->expectException(EmptyIdException::class);
        $id = $objectIdResolver->getObjectId($object);
    }

    public function testNoGetIdDefaultObjectIdResolver(): void
    {
        $objectIdResolver = new DefaultObjectIdResolver();
        $object = new EntityWithoutId();
        $this->expectException(MethodNotFoundException::class);
        $id = $objectIdResolver->getObjectId($object);
    }
}
