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

namespace Rekalogika\File\Tests\App\Controller;

use Rekalogika\File\Association\Contracts\ObjectManagerInterface;
use Rekalogika\File\TemporaryFile;
use Rekalogika\File\Tests\Tests\Model\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TestController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ObjectManagerInterface $objectManager): Response
    {
        // create
        $entity = new Entity('1');

        $newFile = TemporaryFile::createFromString('testContent');
        $newFile->setName('newname.txt');

        $entity->setFile($newFile);

        $objectManager->flushObject($entity);

        return $this->render('index.html.twig');
    }

}
