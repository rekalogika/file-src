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
use Rekalogika\Contracts\File\Trait\FileDecoratorTrait;

class FileDecorator implements FileInterface
{
    use FileDecoratorTrait;

    public function __construct(
        private FileInterface $file,
        private EmbeddedMetadata $metadata
    ) {
    }

    protected function getWrapped(): FileInterface
    {
        return $this->file;
    }

    public function get(string $id)
    {
        /** @psalm-suppress MixedReturnStatement */
        return $this->file->get($id);
    }
}
