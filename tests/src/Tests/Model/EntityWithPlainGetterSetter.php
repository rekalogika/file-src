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

use Rekalogika\Contracts\File\FileInterface;

final class EntityWithPlainGetterSetter
{
    public function __construct(
        private ?FileInterface $file = null,
    ) {}

    public function getFile(): ?FileInterface
    {
        return $this->file;
    }

    public function setFile(?FileInterface $file): self
    {
        $this->file = $file;

        return $this;
    }
}
