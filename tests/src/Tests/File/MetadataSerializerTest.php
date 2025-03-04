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

namespace Rekalogika\File\Tests\Tests\File;

use PHPUnit\Framework\TestCase;
use Rekalogika\Contracts\File\RawMetadataInterface;
use Rekalogika\File\File;
use Rekalogika\File\MetadataSerializer\MetadataSerializer;
use Rekalogika\File\RawMetadata;

final class MetadataSerializerTest extends TestCase
{
    public function testSerialize(): void
    {
        $file = new File(__DIR__ . '/../Resources/smiley.png');
        $metadata = $file->get(RawMetadataInterface::class);
        $this->assertInstanceOf(RawMetadata::class, $metadata);

        $serializer = new MetadataSerializer();
        $result = $serializer->serialize($metadata);
        $this->assertMatchesRegularExpression('{"file.name":"smiley.png","file.size":9852,"file.modificationTime":\d+,"file.type":"image/png","media.width":240,"media.height":240}', $result);
    }

    public function testDeserialize(): void
    {
        $string = '{"file.name":"smiley.png","file.size":9852,"file.modificationTime":1458062751,"file.type":"image/png","media.width":240,"media.height":240}';

        $serializer = new MetadataSerializer();
        $result = $serializer->deserialize($string);
        $this->assertInstanceOf(RawMetadata::class, $result);
        $this->assertSame('smiley.png', $result->get('file.name'));
        $this->assertSame(9852, $result->get('file.size'));
        $this->assertIsInt($result->get('file.modificationTime'));
        $this->assertSame('image/png', $result->get('file.type'));
        $this->assertSame(240, $result->get('media.width'));
        $this->assertSame(240, $result->get('media.height'));
    }

    public function testLegacyDeserialize(): void
    {
        $string = file_get_contents(__DIR__ . '/../Resources/legacyMetadata.json');
        $this->assertNotFalse($string);

        $serializer = new MetadataSerializer();
        $result = $serializer->deserialize($string);

        $this->assertInstanceOf(RawMetadata::class, $result);
        $this->assertSame('inline', $result->get('http._disposition'));
        $this->assertNull($result->get('file.name'));
        $this->assertSame(4, $result->get('file.size'));
        $this->assertSame('text/plain', $result->get('file.type'));
        $this->assertSame(1694957196, $result->get('file.modificationTime'));
    }

    public function testLegacyImageDeserialize(): void
    {
        $string = file_get_contents(__DIR__ . '/../Resources/legacyImageMetadata.json');
        $this->assertNotFalse($string);

        $serializer = new MetadataSerializer();
        $result = $serializer->deserialize($string);

        $this->assertInstanceOf(RawMetadata::class, $result);
        $this->assertSame('inline', $result->get('http._disposition'));
        $this->assertSame(97481, $result->get('file.size'));
        $this->assertSame('image/jpeg', $result->get('file.type'));
        $this->assertSame(1693631289, $result->get('file.modificationTime'));
        $this->assertSame(1024, $result->get('media.width'));
        $this->assertSame(1024, $result->get('media.height'));
    }
}
