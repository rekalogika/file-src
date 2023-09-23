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

use Psr\Container\NotFoundExceptionInterface;
use Rekalogika\Contracts\File\FileInterface;

class AssociatedObjectNotFoundException extends FileException implements
    NotFoundExceptionInterface
{
    public function __construct(
        string $id,
        FileInterface $file,
        \Throwable $previous = null
    ) {
        parent::__construct(sprintf(
            'Unable to get the associated object "%s" of the file object "%s", with filesystem identifier "%s" and key "%s"',
            $id,
            static::class,
            $file->getFilesystemIdentifier() ?? '(null)',
            $file->getKey()
        ), 0, $previous);
    }
}
