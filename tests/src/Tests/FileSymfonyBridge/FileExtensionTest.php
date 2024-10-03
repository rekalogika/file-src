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

namespace Rekalogika\File\Tests\Tests\FileSymfonyBridge;

use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\File\Bridge\Symfony\Form\FileTypeExtension;
use Rekalogika\File\LocalTemporaryFile;
use Rekalogika\File\Tests\Tests\File\FileTestTrait;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationRequestHandler;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileExtensionTest extends TypeTestCase
{
    use FileTestTrait;

    public function testFileType(): void
    {
        $temporaryFile = LocalTemporaryFile::create();
        file_put_contents($temporaryFile->getPathname(), 'foo');
        $file = new UploadedFile($temporaryFile->getPathname(), 'foo.txt', null, 0, true);

        $form = $this->factory->createBuilder(FileType::class)
            ->setRequestHandler(new HttpFoundationRequestHandler())
            ->getForm();

        /** @psalm-suppress InvalidArgument */
        $form->submit($file); // @phpstan-ignore-line
        $this->assertTrue($form->isSynchronized());

        $result = $form->getData();
        $this->assertInstanceOf(FileInterface::class, $result);

        $this->assertFileInterface(
            file: $result,
            filesystemIdentifier: null,
            key: $temporaryFile->getPathname(),
            fileName: 'foo.txt',
            content: 'foo',
            type: 'text/plain',
        );
    }

    /**
     * @return array<array-key,object>
     */
    #[\Override]
    protected function getTypeExtensions(): array
    {
        return [
            new FileTypeExtension(),
        ];
    }
}
