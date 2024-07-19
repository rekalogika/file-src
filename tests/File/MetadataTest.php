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

namespace Rekalogika\File\Tests\File;

use PHPUnit\Framework\TestCase;
use Rekalogika\Domain\File\Metadata\Constants;
use Rekalogika\Domain\File\Metadata\Metadata\FileMetadata;
use Rekalogika\Domain\File\Metadata\Metadata\HttpMetadata;
use Rekalogika\File\RawMetadata;

class MetadataTest extends TestCase
{
    private string $originalTimeZone = 'UTC';

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalTimeZone = \date_default_timezone_get();
    }

    protected function tearDown(): void
    {
        /** @psalm-suppress ArgumentTypeCoercion */
        \date_default_timezone_set($this->originalTimeZone);

        parent::tearDown();
    }

    public function testHttpMetadataUtcTimezone(): void
    {
        \date_default_timezone_set('UTC');

        $rawMetadata = new RawMetadata();
        $rawMetadata->set(Constants::FILE_MODIFICATION_TIME, 1234567890);

        $httpMetadata = HttpMetadata::create($rawMetadata);
        $headers = [];
        foreach($httpMetadata->getHeaders() as $key => $value) {
            $headers[$key] = $value;
        }

        $lastModified = $headers['Last-Modified'];
        $this->assertSame('Fri, 13 Feb 2009 23:31:30 GMT', $lastModified);
    }

    public function testHttpMetadataWibTimezone(): void
    {
        \date_default_timezone_set('Asia/Jakarta');

        $rawMetadata = new RawMetadata();
        $rawMetadata->set(Constants::FILE_MODIFICATION_TIME, 1234567890);

        $httpMetadata = HttpMetadata::create($rawMetadata);
        $headers = [];
        foreach($httpMetadata->getHeaders() as $key => $value) {
            $headers[$key] = $value;
        }

        $lastModified = $headers['Last-Modified'];
        $this->assertSame('Fri, 13 Feb 2009 23:31:30 GMT', $lastModified);
    }

    public function testFileMetadataUtcTimezone(): void
    {
        \date_default_timezone_set('UTC');

        $rawMetadata = new RawMetadata();
        $rawMetadata->set(Constants::FILE_MODIFICATION_TIME, 1234567890);

        $fileMetadata = FileMetadata::create($rawMetadata);
        $modificationTime = $fileMetadata->getModificationTime();

        $this->assertSame('UTC', $modificationTime->getTimezone()->getName());
        $this->assertSame('2009-02-13 23:31:30', $modificationTime->format('Y-m-d H:i:s'));
    }

    public function testFileMetadataWibTimezone(): void
    {
        \date_default_timezone_set('Asia/Jakarta');

        $rawMetadata = new RawMetadata();
        $rawMetadata->set(Constants::FILE_MODIFICATION_TIME, 1234567890);

        $fileMetadata = FileMetadata::create($rawMetadata);
        $modificationTime = $fileMetadata->getModificationTime();

        $this->assertSame('Asia/Jakarta', $modificationTime->getTimezone()->getName());
        $this->assertSame('2009-02-14 06:31:30', $modificationTime->format('Y-m-d H:i:s'));
    }
}
