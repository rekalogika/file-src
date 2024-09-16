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

use Rekalogika\Contracts\File\DirectoryInterface;
use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\File\Zip\Model\Directory;
use Rekalogika\TemporaryUrl\Attribute\AsTemporaryUrlResourceTransformer;
use Rekalogika\TemporaryUrl\Attribute\AsTemporaryUrlServer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class DirectoryResourceServer
{
    public function __construct(
        private FileZip $fileZip,
        private TranslatorInterface $translator,
    ) {}

    /**
     * @param DirectoryInterface<array-key,FileInterface> $files
     */
    #[AsTemporaryUrlResourceTransformer]
    public function transform(DirectoryInterface $files): Directory
    {
        $name = $files->getName();
        $name->setExtension('zip');
        $name = $name->trans($this->translator);

        $directory = new Directory($name);

        foreach ($files as $file) {
            $directory->addPointer($file->getPointer());
        }

        return $directory;
    }

    #[AsTemporaryUrlServer]
    public function respond(Directory $directory): Response
    {
        return $this->fileZip->createZipResponse($directory);
    }
}
