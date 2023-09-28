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

use Rekalogika\Contracts\File\RawMetadataInterface;

final class EmbeddableMetadata implements RawMetadataInterface
{
    public function get(string $key): int|string|bool|null
    {
    }

    public function set(string $key, int|string|bool|null $value): void
    {
    }

    public function delete(string $key): void
    {
    }

    public function merge(iterable $metadata): void
    {
    }

    public function count(): int
    {
    }
}
