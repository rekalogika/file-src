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

namespace Rekalogika\Domain\File\Association\Entity;

use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\Contracts\File\RawMetadataInterface;
use Rekalogika\Contracts\File\Trait\FileDecoratorTrait;
use Rekalogika\Domain\File\Metadata\MetadataFactory;

class FileDecorator implements FileInterface
{
    use FileDecoratorTrait;

    public static function getFile(
        ?FileInterface $file,
        EmbeddedMetadata $metadata
    ): ?self {
        if (null === $file) {
            return null;
        }

        return new self($file, $metadata);
    }

    public static function setFile(
        ?FileInterface $input,
        ?FileInterface &$file,
        EmbeddedMetadata $metadata
    ): void {
        if ($input === null) {
            $file = null;
            $metadata->clear();

            return;
        }

        $file = $input;
        $metadata->merge($input->get(RawMetadataInterface::class));
    }

    private function __construct(
        private FileInterface $file,
        private RawMetadataInterface $metadata
    ) {
    }

    protected function getWrapped(): FileInterface
    {
        return $this->file;
    }

    public function get(string $id)
    {
        if ($id == RawMetadataInterface::class) {
            return new FileMetadataDecorator(
                $this->metadata,
                $this->file->get(RawMetadataInterface::class),
            );
        }

        /** @psalm-suppress MixedReturnStatement */
        return MetadataFactory::create($this->get(RawMetadataInterface::class))
            ->get($id);
    }
}
