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

namespace Rekalogika\File\Tests\FileAssociation;

use PHPUnit\Framework\TestCase;
use Rekalogika\File\Association\FileLocationResolver\DefaultFileLocationResolver;
use Rekalogika\File\Association\ObjectIdResolver\DefaultObjectIdResolver;
use Rekalogika\File\Tests\Model\Entity;

class DefaultFileLocationResolverTest extends TestCase
{
    public function testDefaultLocationResolver(): void
    {
        $objectIdResolver = new DefaultObjectIdResolver();
        $locationResolver = new DefaultFileLocationResolver($objectIdResolver);
        $object = new Entity('entity_id');

        $location = $locationResolver->getFileLocation($object, 'file');
        $this->assertSame('default', $location->getFilesystemIdentifier());
        $this->assertSame('entity/ffa87ef3fc5388bc8b666e2cec17d27cc493d0c1/file/50/a7/74/1b/entity_id', $location->getKey());
    }
}
