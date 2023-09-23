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

namespace Rekalogika\File\Tests\FileSymfonyBridge;

use PHPUnit\Framework\TestCase;
use Rekalogika\File\Bridge\Symfony\HttpFoundation\FileResponse;
use Rekalogika\File\File;

class FileResponseTest extends TestCase
{
    public function testResponse(): void
    {
        $file = new File(__DIR__ . '/../Resources/localFile.txt');

        $response = new FileResponse($file);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('text/plain', $response->headers->get('content-type'));
        $this->assertSame('inline; filename="localFile.txt"', $response->headers->get('content-disposition'));
        $this->assertSame('4', $response->headers->get('content-length'));

        ob_start();
        $response->send();
        $content = ob_get_clean();

        $this->assertSame('test', $content);
    }

    public function testResponseWithForcedDisposition(): void
    {
        $file = new File(__DIR__ . '/../Resources/localFile.txt');

        $response = new FileResponse($file, disposition: 'attachment');
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('text/plain', $response->headers->get('content-type'));
        $this->assertSame('attachment; filename="localFile.txt"', $response->headers->get('content-disposition'));
        $this->assertSame('4', $response->headers->get('content-length'));

        ob_start();
        $response->send();
        $content = ob_get_clean();

        $this->assertSame('test', $content);
    }

    public function testResponseWithExtraHeaders(): void
    {
        $file = new File(__DIR__ . '/../Resources/localFile.txt');

        $response = new FileResponse($file, headers: [
            'x-foo' => 'bar',
        ]);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('bar', $response->headers->get('x-foo'));
    }
}
