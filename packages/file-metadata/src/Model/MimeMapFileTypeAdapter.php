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

use FileEye\MimeMap\MalformedTypeException;
use FileEye\MimeMap\MappingException;
use FileEye\MimeMap\Type;
use Rekalogika\Contracts\File\FileTypeInterface;
use Symfony\Contracts\Translation\TranslatableInterface;

final class MimeMapFileTypeAdapter implements FileTypeInterface
{
    private readonly string $type;

    private ?Type $parsedType = null;

    public function __construct(string $type)
    {
        $type = strtolower($type);
        $type = trim($type);
        $this->type = $type;
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->getParsed()->toString();
    }

    private function getParsed(): Type
    {
        if ($this->parsedType !== null) {
            return $this->parsedType;
        }

        try {
            return $this->parsedType = new Type($this->type);
        } catch (MalformedTypeException | MappingException) {
            return $this->parsedType = new Type('application/octet-stream');
        }
    }

    #[\Override]
    public function getName(): string
    {
        return $this->getParsed()->toString();
    }

    #[\Override]
    public function getType(): string
    {
        return $this->getParsed()->getMedia();
    }

    #[\Override]
    public function getSubType(): string
    {
        return $this->getParsed()->getSubType();
    }

    #[\Override]
    public function getCommonExtensions(): array
    {
        return $this->getParsed()->getExtensions();
    }

    #[\Override]
    public function getExtension(): ?string
    {
        try {
            return $this->getParsed()->getDefaultExtension();
        } catch (MappingException) {
            return null;
        }
    }

    #[\Override]
    public function getDescription(): \Stringable&TranslatableInterface
    {
        try {
            return new TranslatableMessage(
                $this->getParsed()->getDescription(),
                '{name}',
                [
                    '{name}' => $this->getParsed()->getDescription(),
                ],
            );
        } catch (MappingException) {
            return new TranslatableMessage(
                \sprintf('Unknown file type (%s)', $this->type),
                'Unknown file type ({type})',
                [
                    '{type}' => $this->type,
                ],
            );
        }
    }
}
