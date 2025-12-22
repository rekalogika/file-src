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

namespace Rekalogika\File\Tests\Tests\Model;

use League\Flysystem\FilesystemOperator;
use Rekalogika\Contracts\File\Exception\File\TemporaryFileException;
use Rekalogika\File\File;

final class FailingTemporaryFile extends File
{
    private bool $failIfGetAccessed = false;

    private function __construct(
        string $key,
        ?FilesystemOperator $filesystem = null,
        ?string $filesystemIdentifier = null,
    ) {
        parent::__construct($key, $filesystem, $filesystemIdentifier);

        $this->setContent('foo');
    }

    public function __destruct()
    {
        $this->getFilesystem()->delete($this->getKey());
    }

    /**
     * Creates a temporary file in the local filesystem
     */
    final public static function create(
        ?string $prefix = null,
    ): self {
        $prefix ??= 'temporaryfile-';
        $path = tempnam(sys_get_temp_dir(), $prefix);

        if ($path === false) {
            throw new TemporaryFileException($prefix);
        }

        $file = new self($path);
        $file->setName(null);

        return $file;
    }

    #[\Override]
    public function get(string $id): mixed
    {
        if ($this->failIfGetAccessed) {
            throw new \Exception('Accessing metadata is not allowed in this test.');
        }

        /** @psalm-suppress MixedReturnStatement */
        return parent::get($id);
    }

    public function setFailIfGetAccessed(bool $value): void
    {
        $this->failIfGetAccessed = $value;
    }
}
