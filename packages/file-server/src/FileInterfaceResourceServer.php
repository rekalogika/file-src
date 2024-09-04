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

namespace Rekalogika\File\Server;

use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\Contracts\File\FilePointerInterface;
use Rekalogika\Contracts\File\FileRepositoryInterface;
use Rekalogika\File\Bridge\Symfony\HttpFoundation\FileResponse;
use Rekalogika\TemporaryUrl\Attribute\AsTemporaryUrlResourceTransformer;
use Rekalogika\TemporaryUrl\Attribute\AsTemporaryUrlServer;
use Symfony\Component\HttpFoundation\Response;

class FileInterfaceResourceServer
{
    public function __construct(
        private readonly FileRepositoryInterface $fileRepository,
    ) {}

    #[AsTemporaryUrlResourceTransformer]
    public function transform(FileInterface $file): FilePointerInterface
    {
        return $file->getPointer();
    }


    #[AsTemporaryUrlServer]
    public function respond(FilePointerInterface $filePointer): Response
    {
        $file = $this->fileRepository->get($filePointer);

        return new FileResponse($file);
    }
}
