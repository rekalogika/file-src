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

use Rekalogika\Contracts\File\FileTypeInterface;
use Symfony\Contracts\Translation\TranslatableInterface;

final class NullType implements FileTypeInterface
{
    public function __construct(
        private readonly string $description,
        private readonly string $translationDomain,
    ) {}

    #[\Override]
    public function getName(): string
    {
        return 'application/x-zerosize';
    }

    #[\Override]
    public function getType(): string
    {
        return 'application';
    }

    #[\Override]
    public function getSubType(): string
    {
        return 'x-zerosize';
    }

    #[\Override]
    public function getCommonExtensions(): array
    {
        return [];
    }

    #[\Override]
    public function getExtension(): ?string
    {
        return null;
    }

    #[\Override]
    public function getDescription(): \Stringable&TranslatableInterface
    {
        return new TranslatableMessage(
            $this->description,
            $this->translationDomain,
        );
    }

    #[\Override]
    public function __toString(): string
    {
        return 'application/x-zerosize';
    }
}
