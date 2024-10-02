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

namespace Rekalogika\File\Tests\Tests\Model;

use Doctrine\ORM\Mapping\Entity;
use Rekalogika\Domain\File\Association\Entity\AbstractFile;

#[Entity()]
class EntityExtendingAbstractFile extends AbstractFile {}
