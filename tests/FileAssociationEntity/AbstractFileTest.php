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

namespace Rekalogika\File\Tests\FileAssociationEntity;

use PHPUnit\Framework\TestCase;
use Rekalogika\Domain\File\Association\Entity\AbstractFile;
use Rekalogika\Domain\File\Association\Entity\EmbeddedMetadata;
use Rekalogika\Domain\File\Association\Entity\UnsetFile;
use Rekalogika\Domain\File\Metadata\Constants;
use Rekalogika\File\TemporaryFile;
use Rekalogika\File\Tests\File\FileTestTrait;
use Rekalogika\File\Tests\Model\EntityExtendingAbstractFile;

class AbstractFileTest extends TestCase
{
    use FileTestTrait;

    public function testAbstractFile(): void
    {
        $file = TemporaryFile::create('test');
        $file->setContent('test-temporary-file');
        $path = $file->getKey();

        $this->assertFileInterface(
            file: $file,
            filesystemIdentifier: null,
            key: $path,
            fileName: 'Untitled.txt',
            content: 'test-temporary-file',
            type: 'text/plain',
        );

        $entity = new EntityExtendingAbstractFile($file);

        $this->assertFileInterface(
            file: $entity,
            filesystemIdentifier: null,
            key: $path,
            fileName: 'Untitled.txt',
            content: 'test-temporary-file',
            type: 'text/plain',
        );
    }

    /**
     * Simulating the case where doctrine did not call the onLoad event.
     */
    public function testUnsetAbstractFile(): void
    {
        $reflection = new \ReflectionClass(EntityExtendingAbstractFile::class);
        $entity = $reflection->newInstanceWithoutConstructor();
        $this->expectException(UnsetFile::class);
        $entity->getContent();
    }

    /**
     * Simulating the case where doctrine did not call the onLoad event, but
     * the entity has embedded metadata.
     */
    public function testUnsetAbstractFileHavingEmbeddedMetadata(): void
    {
        $reflectionClass = new \ReflectionClass(EntityExtendingAbstractFile::class);
        $abstractFileReflection = new \ReflectionClass(AbstractFile::class);

        $entity = $reflectionClass->newInstanceWithoutConstructor();

        $metadata = new EmbeddedMetadata();
        $metadata->set(Constants::FILE_NAME, 'foo.txt');
        $metadata->set(Constants::FILE_SIZE, 123);
        $metadata->set(Constants::FILE_TYPE, 'text/plain');
        $metadata->set(Constants::FILE_MODIFICATION_TIME, 1234567890);

        $reflectionProperty = $abstractFileReflection->getProperty('metadata');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($entity, $metadata);

        static::assertSame('foo.txt', (string) $entity->getName()->getFull());
        static::assertSame(123, $entity->getSize());
        static::assertSame('text/plain', $entity->getType()->getName());
        static::assertSame(1234567890, $entity->getLastModified()->getTimestamp());

        $this->expectException(UnsetFile::class);
        $entity->getContent();
    }
}
