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

namespace Rekalogika\File\Tests\FileBundle;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Rekalogika\File\Tests\TestKernel;

class IntegrationTest extends TestCase
{
    private ?ContainerInterface $container = null;

    public function setUp(): void
    {
        $kernel = new TestKernel();
        $kernel->boot();
        $this->container = $kernel->getContainer();
    }

    public function testWiring(): void
    {
        foreach (TestKernel::getServiceIds() as $serviceId) {
            $this->assertInstanceOf(
                $serviceId,
                $this->container?->get('test.' . $serviceId)
            );
        }
    }

}
