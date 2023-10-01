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
use Rekalogika\Contracts\File\FileProxy;
use Rekalogika\Contracts\File\Null\NullFile;
use Rekalogika\Contracts\File\RawMetadataInterface;
use Rekalogika\Contracts\File\Trait\FileDecoratorTrait;
use Rekalogika\Domain\File\Metadata\MetadataFactory;

class FileDecorator implements FileInterface
{
    use FileDecoratorTrait;

    //
    // static methods
    //

    public static function getFile(
        ?FileInterface $file,
        EmbeddedMetadata $metadata
    ): ?self {
        if ($metadata->isFilePresent() === false) {
            // metadata says the file does not exist, and the file does not
            // exists, too. so we return null
            if ($file === null) {
                return null;

            // metadata says the file does not exist, but we get a lazy-loading
            // proxy that we don't know whether the real file exists or not. it
            // is highly probable the real file does not exist.
            } elseif ($file instanceof FileProxy) {
                return null;

            // metadata says the file does not exist, but the file exists.
            // we sync the metadata to the entity and return the file. if by
            // any chance the caller calls `flush()`, the metadata will be
            // persisted.
            } else {
                $metadata->merge($file->get(RawMetadataInterface::class));

                return new self($file, $metadata);
            }
        }

        // metadata indicates the file should exist, but it is not. therefore,
        // we use NullFile to represent the missing file.
        if (null === $file) {
            $file = new NullFile();
        }

        return new self($file, $metadata);
    }

    public static function setFile(
        ?FileInterface $input,
        ?FileInterface &$file,
        EmbeddedMetadata $metadata
    ): void {
        $metadata->clear();

        if ($input === null) {
            $file = null;

            return;
        }

        $file = $input;
        $metadata->merge($input->get(RawMetadataInterface::class));
    }

    //
    // constructor
    //

    private function __construct(
        private FileInterface $file,
        private EmbeddedMetadata $metadata
    ) {
    }

    //
    // other methods
    //

    /**
     * Resynchronizes metadata from the file to the metadata stored in the entity.
     *
     * @return void
     */
    public static function syncMetadata(FileInterface $file): void
    {
        if (!$file instanceof self) {
            throw new \InvalidArgumentException(
                sprintf('"syncMetadata()" only accepts %s', static::class)
            );
        }

        $file->metadata->clear();
        $file->metadata->merge(
            $file->file->get(RawMetadataInterface::class)
        );
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
