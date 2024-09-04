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

namespace Rekalogika\File;

use Oneup\UploaderBundle\Uploader\File\FileInterface as OneupUploaderFileInterface;
use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\File\Adapter\FromSplFileInfoAdapter;
use Rekalogika\File\Bridge\OneupUploader\FromOneUpUploaderFileAdapter;
use Rekalogika\File\Bridge\Symfony\HttpFoundation\FromHttpFoundationFileAdapter;
use Symfony\Component\HttpFoundation\File\File as HttpFoundationFile;

/**
 * Universal adapter. Converts any supported object to a FileInterface.
 */
class FileAdapter
{
    private function __construct() {}

    public static function adapt(string|object $source): FileInterface
    {
        if (\is_string($source)) {
            return new File($source);
        } elseif ($source instanceof FileInterface) {
            return $source;
        } elseif (class_exists(HttpFoundationFile::class) && $source instanceof HttpFoundationFile) {
            return FromHttpFoundationFileAdapter::adapt($source);
        } elseif ($source instanceof \SplFileInfo) {
            return FromSplFileInfoAdapter::adapt($source);
        } elseif (class_exists(OneupUploaderFileInterface::class) && $source instanceof OneupUploaderFileInterface) {
            return FromOneUpUploaderFileAdapter::adapt($source);
        }

        throw new \InvalidArgumentException(\sprintf(
            'Converting "%s" to a "%s" is not supported',
            get_debug_type($source),
            FileInterface::class,
        ));
    }
}
