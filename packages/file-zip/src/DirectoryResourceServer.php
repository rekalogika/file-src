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

use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\Contracts\File\FilesInterface;
use Rekalogika\File\Zip\Model\Directory;
use Rekalogika\TemporaryUrl\Attribute\AsTemporaryUrlResourceTransformer;
use Rekalogika\TemporaryUrl\Attribute\AsTemporaryUrlServer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

final class DirectoryResourceServer
{
    public function __construct(
        private FileZip $fileZip,
        private TranslatorInterface $translator,
    ) {
    }

    /**
     * @param FilesInterface<array-key,FileInterface> $files
     */
    #[AsTemporaryUrlResourceTransformer]
    public function transform(FilesInterface $files): Directory
    {
        $name = $files->getName()->trans($this->translator);
        $directory = new Directory($name);

        foreach ($files as $file) {
            $directory->addPointer($file->getPointer());
        }

        return $directory;
    }

    #[AsTemporaryUrlServer]
    public function respond(Directory $directory): Response
    {
        $response = new StreamedResponse(function () use ($directory) {
            $this->fileZip->streamZip($directory);
        });

        return $response;
    }
}
