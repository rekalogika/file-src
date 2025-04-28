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

namespace Rekalogika\File\Tests\App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\File\Association\Attribute\AsFileAssociation;
use Rekalogika\File\Association\Attribute\WithFileAssociation;

#[ORM\Entity]
#[WithFileAssociation]
class User
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: Types::INTEGER)]
    private ?int $id = null; // @phpstan-ignore property.unusedType

    #[AsFileAssociation]
    private ?FileInterface $image = null;

    public function __construct(#[ORM\Column(type: Types::STRING, length: 255)]
        private string $name) {}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getImage(): ?FileInterface
    {
        return $this->image;
    }

    public function setImage(?FileInterface $image): self
    {
        $this->image = $image;

        return $this;
    }
}
