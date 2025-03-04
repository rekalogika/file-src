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

namespace Rekalogika\File\Bridge\OneupUploader;

use League\Flysystem\FilesystemOperator;
use Oneup\UploaderBundle\Uploader\File\FileInterface as TheirFileInterface;
use Rekalogika\Contracts\File\FileInterface as OurFileInterface;
use Rekalogika\File\File;

/**
 * Adapter to convert a OneUpUploader's FileInterface into our FileInterface
 */
class FromOneUpUploaderFileAdapter extends File
{
    private function __construct(TheirFileInterface $file)
    {
        /** @psalm-suppress MixedAssignment */
        $filesystem = $file->getFileSystem();

        if ($filesystem === null) {
            $ourFilesystem = null;
        } elseif (class_exists(FilesystemOperator::class) && $filesystem instanceof FilesystemOperator) {
            $ourFilesystem = $filesystem;
        } else {
            throw new \InvalidArgumentException('Unsupported filesystem type: ' . get_debug_type($filesystem));
        }

        parent::__construct($file->getPathname(), $ourFilesystem);
    }

    public static function adapt(TheirFileInterface $file): OurFileInterface
    {
        return new self($file);
    }
}
