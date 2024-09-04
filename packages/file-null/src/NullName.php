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
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class NullName implements FileNameInterface
{
    public function __construct(
        private readonly string $name,
        private readonly string $translationDomain,
    ) {}

    #[\Override]
    public function getFull(): \Stringable&TranslatableInterface
    {
        return $this->getBase();
    }

    #[\Override]
    public function setFull(string $name): void {}

    #[\Override]
    public function getBase(): \Stringable&TranslatableInterface
    {
        return new TranslatableMessage(
            $this->name,
            $this->translationDomain,
        );
    }

    #[\Override]
    public function setBase(string $name): void {}

    #[\Override]
    public function getExtension(): ?string
    {
        return null;
    }

    #[\Override]
    public function setExtension(?string $extension): void {}

    #[\Override]
    public function hasExtension(): bool
    {
        return false;
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->name;
    }

    #[\Override]
    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $this->getBase()->trans($translator, $locale);
    }
}
