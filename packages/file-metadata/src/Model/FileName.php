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

final class FileName implements FileNameInterface
{
    private ?string $name = null;
    private ?string $extension = null;

    public function __construct(
        ?string $filename,
        ?string $forceExtension = null
    ) {
        if ($filename !== null) {
            $this->parse($filename);
        }

        if ($forceExtension !== null) {
            $this->extension = $forceExtension;
        }
    }

    private function parse(string $filename): void
    {
        $pathinfo = pathinfo($filename);

        $this->name = $pathinfo['filename'] === '' ? null : $pathinfo['filename'];
        $this->extension = isset($pathinfo['extension']) ? strtolower($pathinfo['extension']) : null;
    }

    public function __toString(): string
    {
        if ($this->name === null) {
            if ($this->extension) {
                return 'Untitled.' . $this->extension;
            }
            return 'Untitled';

        }

        return (string) $this->getFull();
    }

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        if ($this->name === null) {
            $full = $this->getFull();
            assert($full instanceof TranslatableInterface);

            return $full->trans($translator, $locale);
        }

        return (string) $this->getFull();
    }

    public function getFull(): \Stringable&TranslatableInterface
    {
        if ($this->name === null) {
            if ($this->extension) {
                return new TranslatableMessage(
                    'Untitled.' . $this->extension,
                    'Untitled.{extension}',
                    [
                        '{extension}' => $this->extension,
                    ]
                );
            }
            return new TranslatableMessage('Untitled', 'Untitled');

        }

        if ($this->extension) {
            return new TranslatableMessage(
                $this->name . '.' . $this->extension,
                '{name}.{extension}',
                [
                    '{name}' => $this->name,
                    '{extension}' => $this->extension,
                ]
            );
        }
        return new TranslatableMessage(
            $this->name,
            '{name}',
            [
                '{name}' => $this->name,
            ]
        );

    }

    public function setFull(string $name): void
    {
        $this->parse($name);
    }

    public function getBase(): \Stringable&TranslatableInterface
    {
        if ($this->name === null) {
            return new TranslatableMessage('Untitled', 'Untitled');
        }
        return new TranslatableMessage(
            $this->name,
            '{name}',
            [
                '{name}' => $this->name,
            ]
        );

    }

    public function setBase(string $name): void
    {
        $this->name = $name;
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
