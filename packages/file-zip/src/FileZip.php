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

namespace Rekalogika\File\Zip;

use Psr\Http\Message\StreamInterface;
use Rekalogika\Contracts\File\Tree\DirectoryInterface;
use ZipStream\ZipStream;

final class FileZip
{
    public function __construct(private ZipDirectory $zipDirectory)
    {
    }

    /**
     * @param resource|StreamInterface|null $outputStream
     */
    public function streamZip(
        string $fileName,
        DirectoryInterface $directory,
        $outputStream = null,
        bool $sendHttpHeaders = true,
    ): void {
        $zip = new ZipStream(
            outputName: $fileName,
            contentType: 'application/zip',
            outputStream: $outputStream,
            sendHttpHeaders: $sendHttpHeaders,
        );

        $this->zipDirectory->with(
            zip: $zip,
            directory: $directory,
            directoryName: '',
            parent: null,
        )->process();

        $zip->finish();
    }
}
