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

namespace Rekalogika\File\Association\Util;

final readonly class FileLocationUtil
{
    private function __construct() {}

    public static function createHashedDirectory(
        string $id,
        int $hashLevel,
    ): string {
        return implode(
            '/',
            \array_slice(
                str_split(sha1($id), 2),
                0,
                $hashLevel,
            ),
        );
    }
}
