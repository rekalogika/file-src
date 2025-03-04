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

namespace Rekalogika\Contracts\File\Exception\File;

final class TemporaryFileException extends FileException
{
    public function __construct(
        string $prefix,
        ?string $filesystemId = null,
        ?\Throwable $previous = null,
    ) {
        if ($filesystemId !== null) {
            parent::__construct(
                \sprintf(
                    'Cannot create a temporary file with prefix "%s" in filesystem "%s"',
                    $prefix,
                    $filesystemId,
                ),
                0,
                $previous,
            );
        } else {
            parent::__construct(
                \sprintf(
                    'Cannot create a temporary file with prefix "%s" in the local filesystem.',
                    $prefix,
                ),
                0,
                $previous,
            );
        }
    }
}
