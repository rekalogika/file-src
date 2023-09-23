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

class FileNotFoundException extends FileException
{
    public function __construct(
        string $key,
        ?string $filesystemId = null,
        \Throwable $previous = null
    ) {
        if ($filesystemId) {
            parent::__construct(
                sprintf(
                    'File "%s" in filesystem "%s" does not exist.',
                    $key,
                    $filesystemId,
                ),
                0,
                $previous
            );
        } else {
            parent::__construct(
                sprintf(
                    'File "%s" does not exist.',
                    $key,
                ),
                0,
                $previous
            );
        }
    }
}
