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

namespace Rekalogika\File\Type;

use FileEye\MimeMap\MalformedTypeException;
use FileEye\MimeMap\MappingException;
use FileEye\MimeMap\Type;
use Rekalogika\Contracts\File\FileTypeInterface;
use Rekalogika\File\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatableInterface;

final class MimeMapFileTypeAdapter implements FileTypeInterface
{
    private string $type;
    private ?Type $parsedType = null;

    public function __construct(string $type)
    {
        $type = strtolower($type);
        $type = \trim($type);
        $this->type = $type;
    }

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

    public function getName(): string
    {
        return $this->getParsed()->toString();
    }

    public function getType(): string
    {
        return $this->getParsed()->getMedia();
    }

    public function getSubType(): string
    {
        return $this->getParsed()->getSubType();
    }

    public function getCommonExtensions(): array
    {
        return $this->getParsed()->getExtensions();
    }

    public function getExtension(): ?string
    {
        try {
            return $this->getParsed()->getDefaultExtension();
        } catch (MappingException) {
            return null;
        }
    }

    public function getDescription(): string|(\Stringable&TranslatableInterface)
    {
        try {
            return $this->getParsed()->getDescription();
        } catch (MappingException) {
            return new TranslatableMessage(
                sprintf('Unknown file type (%s)', $this->type),
                'Unknown file type ({type})',
                [
                    '{type}' => $this->type
                ],
            );
        }
    }
}
