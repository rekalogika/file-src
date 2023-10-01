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

namespace Rekalogika\Contracts\File\Null;

use Rekalogika\Contracts\File\FileNameInterface;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class NullName implements FileNameInterface
{
    public function getFull(): \Stringable&TranslatableInterface
    {
        return $this->getBase();
    }

    public function setFull(string $name): void
    {
    }

    public function getBase(): \Stringable&TranslatableInterface
    {
        return new class implements \Stringable, TranslatableInterface {
            public function __toString(): string
            {
                return 'Null';
            }

            public function trans(
                TranslatorInterface $translator,
                ?string $locale = null
            ): string {
                return $translator->trans(
                    'Null',
                    [],
                    'rekalogika_file',
                    $locale
                );
            }
        };
    }

    public function setBase(string $name): void
    {
    }

    public function getExtension(): ?string
    {
        return null;
    }

    public function setExtension(?string $extension): void
    {
    }

    public function hasExtension(): bool
    {
        return false;
    }

    public function __toString(): string
    {
        return 'Null';
    }

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $this->getBase()->trans($translator, $locale);
    }
}
