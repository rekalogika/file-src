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

namespace Rekalogika\File\Association\Model;

use Rekalogika\Contracts\File\FileNameInterface;
use Rekalogika\Contracts\File\FileTypeInterface;
use Rekalogika\Contracts\File\NullFileInterface;
use Rekalogika\Domain\File\Null\NullFileTrait;
use Rekalogika\Domain\File\Null\NullName;
use Rekalogika\Domain\File\Null\NullType;

/**
 * A null file that indicates the file is expected to exist, but missing in the
 * storage backend.
 */
class MissingFile extends \Exception implements NullFileInterface
{
    use NullFileTrait;

    public function __construct(
        private ?string $filesystemIdentifier,
        private string $key,
    ) {
        if ($filesystemIdentifier === null) {
            parent::__construct(sprintf(
                'File with key "%s" is missing in the local filesystem',
                $key,
            ));
        } else {
            parent::__construct(sprintf(
                'File with key "%s" is missing in filesystem "%s"',
                $key,
                $filesystemIdentifier
            ));
        }
    }

    #[\Override]
    public function getFilesystemIdentifier(): ?string
    {
        return $this->filesystemIdentifier;
    }

    #[\Override]
    public function getKey(): string
    {
        return $this->key;
    }

    #[\Override]
    public function getName(): FileNameInterface
    {
        return new NullName('(missing)', 'rekalogika_file');
    }

    #[\Override]
    public function getType(): FileTypeInterface
    {
        return new NullType('The file is missing in the storage media', 'rekalogika_file');
    }
}
