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

namespace Rekalogika\File\Tests\Model;

use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\File\Association\Attribute\AsFileAssociation;
use stdClass;

class EntityWithDifferentFileProperties
{
    #[AsFileAssociation(fetch: 'EAGER')]
    protected ?FileInterface $nullableEager = null;

    #[AsFileAssociation(fetch: 'EAGER')]
    protected FileInterface $notNullableEager;

    #[AsFileAssociation(fetch: 'LAZY')]
    protected ?FileInterface $nullableLazy = null;

    #[AsFileAssociation(fetch: 'LAZY')]
    protected FileInterface $notNullableLazy;

    #[AsFileAssociation]
    protected stdClass $nonFileProperty;

    protected FileInterface $fileWithoutAttribute;
}
