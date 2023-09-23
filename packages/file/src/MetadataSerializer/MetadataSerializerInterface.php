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

namespace Rekalogika\File\MetadataSerializer;

use Rekalogika\Contracts\File\RawMetadataInterface;

/**
 * Serialize and deserialize metadata
 */
interface MetadataSerializerInterface
{
    public function serialize(RawMetadataInterface $metadata): string;
    public function deserialize(string $serialized): RawMetadataInterface;
}
