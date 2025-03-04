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

namespace Rekalogika\Domain\File\Null;

use Rekalogika\Contracts\File\FileNameInterface;
use Rekalogika\Contracts\File\FileTypeInterface;
use Rekalogika\Contracts\File\NullFileInterface;

/**
 * A null-value pattern implementation for FileInterface.
 */
final class NullFile implements NullFileInterface
{
    use NullFileTrait;

    public function __construct(
        private ?string $filesystemIdentifier = null,
        private ?string $key = null,
    ) {}

    #[\Override]
    public function getFilesystemIdentifier(): ?string
    {
        return $this->filesystemIdentifier;
    }

    #[\Override]
    public function getKey(): string
    {
        return $this->key ?? '/dev/null';
    }

    #[\Override]
    public function getName(): FileNameInterface
    {
        return new NullName('Null', 'rekalogika_file');
    }

    #[\Override]
    public function getType(): FileTypeInterface
    {
        return new NullType('Null file', 'rekalogika_file');
    }
}
