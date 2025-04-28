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

namespace Rekalogika\File\Tests\Tests\FileAssociation;

use PHPUnit\Framework\TestCase;
use Rekalogika\File\Association\Util\ProxyUtil;

final class ProxyUtilTest extends TestCase
{
    /**
     * @dataProvider provideTestProxy
     */
    public function testProxy(string $class, string $expected): void
    {
        $this->assertTrue(class_exists($class), \sprintf(
            'Class "%s" does not exist',
            $class,
        ));

        $this->assertTrue(class_exists($expected), \sprintf(
            'Class "%s" does not exist',
            $expected,
        ));

        $this->assertSame(ProxyUtil::normalizeClassName($class), $expected);
    }

    /**
     * @return iterable<string,array{string,string}>
     */
    public static function provideTestProxy(): iterable
    {
        yield 'Doctrine ORM' => [
            'Proxies\__CG__\Rekalogika\File\Tests\App\Entity\DummyEntity',
            'Rekalogika\File\Tests\App\Entity\DummyEntity',
        ];

        yield 'Doctrine ODM' => [
            'MongoDBODMProxies\__PM__\Rekalogika\File\Tests\App\Entity\DummyEntity\Generated93deedc1e7b56ba9c8d5a337a376eda9',
            'Rekalogika\File\Tests\App\Entity\DummyEntity',
        ];
    }
}

namespace Proxies\__CG__\Rekalogika\File\Tests\App\Entity;

class DummyEntity extends \Rekalogika\File\Tests\App\Entity\DummyEntity {}

namespace MongoDBODMProxies\__PM__\Rekalogika\File\Tests\App\Entity\DummyEntity;

use Rekalogika\File\Tests\App\Entity\DummyEntity;

class Generated93deedc1e7b56ba9c8d5a337a376eda9 extends DummyEntity {}
