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

namespace Rekalogika\Domain\File\Metadata\Model;

use Rekalogika\Contracts\File\FileNameInterface;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Translatable file name for your own use, like inside entities.
 */
final class TranslatableFileName implements FileNameInterface
{
    public function __construct(
        private TranslatableInterface&\Stringable $base,
        private ?string $extension = null
    ) {
    }

    public function __toString(): string
    {
        return (string) $this->getFull();
    }

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $this->getFull()->trans($translator, $locale);
    }

    public function getFull(): \Stringable&TranslatableInterface
    {
        if ($this->extension) {
            return new TranslatableMessage(
                sprintf('%s.%s', (string) $this->base, $this->extension),
                '{name}.{extension}',
                [
                    '{name}' => $this->base,
                    '{extension}' => $this->extension,
                ]
            );
        } else {
            return new TranslatableMessage(
                (string) $this->base,
                '{name}',
                [
                    '{name}' => $this->base,
                ]
            );
        }
    }

    public function setFull(string $name): void
    {
        throw new \BadMethodCallException('Cannot set base name on translatable file name');
    }

    public function getBase(): \Stringable&TranslatableInterface
    {
        return $this->base;
    }

    public function setBase(string $name): void
    {
        throw new \BadMethodCallException('Cannot set base name on translatable file name');
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(?string $extension): void
    {
        if ($extension === '') {
            $extension = null;
        }

        $this->extension = $extension;
    }

    public function hasExtension(): bool
    {
        return $this->extension !== '' && $this->extension !== null;
    }
}
