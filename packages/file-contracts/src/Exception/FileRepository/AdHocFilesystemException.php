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

namespace Rekalogika\Contracts\File\Exception\FileRepository;

use Rekalogika\Contracts\File\FileInterface;

class AdHocFilesystemException extends FileRepositoryException
{
    public function __construct(
        FileInterface $file,
        \Throwable $previous = null
    ) {
        parent::__construct(sprintf(
            'File with key "%s" has an ad-hoc filesystem "%s", but the function you are using in the file repository is unable work with such a file.',
            $file->getKey(),
            $file->getFilesystemIdentifier() ?? 'null'
        ), 0, $previous);
    }
}
