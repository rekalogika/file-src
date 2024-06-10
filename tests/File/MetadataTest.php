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
use Rekalogika\Domain\File\Metadata\Metadata\HttpMetadata;
use Rekalogika\File\RawMetadata;

class MetadataTest extends TestCase
{
    public function testHttpMetadataUtcTimezone(): void
    {
        $oldTimeZone = \date_default_timezone_get();
        \date_default_timezone_set('UTC');

        $rawMetadata = new RawMetadata();
        $rawMetadata->set(Constants::FILE_MODIFICATION_TIME, 1234567890);

        $httpMetadata = HttpMetadata::create($rawMetadata);
        /** @psalm-suppress InvalidArgument */
        $headers = iterator_to_array($httpMetadata->getHeaders());

        $lastModified = $headers['Last-Modified'];
        $this->assertSame('Fri, 13 Feb 2009 23:31:30 GMT', $lastModified);

        \date_default_timezone_set($oldTimeZone);
    }

    public function testHttpMetadataWibTimezone(): void
    {
        $oldTimeZone = \date_default_timezone_get();
        \date_default_timezone_set('Asia/Jakarta');

        $rawMetadata = new RawMetadata();
        $rawMetadata->set(Constants::FILE_MODIFICATION_TIME, 1234567890);

        $httpMetadata = HttpMetadata::create($rawMetadata);
        /** @psalm-suppress InvalidArgument */
        $headers = iterator_to_array($httpMetadata->getHeaders());

        $lastModified = $headers['Last-Modified'];
        $this->assertSame('Fri, 13 Feb 2009 23:31:30 GMT', $lastModified);

        \date_default_timezone_set($oldTimeZone);
    }
}
