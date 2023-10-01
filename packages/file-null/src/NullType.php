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

class NullType implements FileTypeInterface
{
    public function __construct(
        private string $description,
        private string $translationDomain
    ) {
    }

    public function getName(): string
    {
        return 'application/x-zerosize';
    }

    public function getType(): string
    {
        return 'application';
    }

    public function getSubType(): string
    {
        return 'x-zerosize';
    }

    public function getCommonExtensions(): array
    {
        return [];
    }

    public function getExtension(): ?string
    {
        return null;
    }

    public function getDescription(): \Stringable&TranslatableInterface
    {
        return new TranslatableMessage(
            $this->description,
            $this->translationDomain
        );
    }

    public function __toString(): string
    {
        return 'application/x-zerosize';
    }
}
