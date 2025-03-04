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

namespace Rekalogika\File\Association\Command;

use Doctrine\Persistence\ManagerRegistry;
use Rekalogika\File\Association\Contracts\FileLocationResolverInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'rekalogika:file:resolve',
    description: 'Resolves the location of the file based on the object and property name.',
)]
final class FileLocationResolverCommand extends Command
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
        private readonly FileLocationResolverInterface $fileLocationResolver,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this->addArgument('class', InputArgument::REQUIRED, 'Class name');
        $this->addArgument('id', InputArgument::REQUIRED, 'Identifier');
        $this->addArgument('property', InputArgument::REQUIRED, 'Property name');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $class = $input->getArgument('class');

        if (!\is_string($class) || !class_exists($class)) {
            throw new \InvalidArgumentException(\sprintf('Class %s not found', get_debug_type($class)));
        }

        $id = $input->getArgument('id');

        if (!\is_string($id)) {
            throw new \InvalidArgumentException(\sprintf('Id %s not found', get_debug_type($id)));
        }

        $propertyName = $input->getArgument('property');

        if (!\is_string($propertyName)) {
            throw new \InvalidArgumentException(\sprintf('Property name %s not found', get_debug_type($propertyName)));
        }

        $object = $this->managerRegistry->getRepository($class)->find($id);

        if (!$object) {
            throw new \InvalidArgumentException('Object not found');
        }

        $filePointer = $this->fileLocationResolver->getFileLocation($object, $propertyName);

        $output->writeln($filePointer->getKey());

        return Command::SUCCESS;
    }
}
