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

namespace Rekalogika\File\Tests\Tests\FileImage;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Rekalogika\Contracts\File\FileRepositoryInterface;
use Rekalogika\File\FilePointer;
use Rekalogika\File\Image\ImageResizer;
use Rekalogika\File\Image\ImageTwigExtension;
use Rekalogika\File\Image\ImageTwigRuntime;
use Rekalogika\File\Tests\Tests\File\FileFactory;
use Twig\RuntimeLoader\FactoryRuntimeLoader;
use Twig\Test\IntegrationTestCase;

final class TwigTest extends IntegrationTestCase
{
    private FileRepositoryInterface $fileRepository;
    private ImageResizer $imageResizer;


    #[\Override]
    protected function setUp(): void
    {
        $this->fileRepository = FileFactory::createFileRepository();
        $this->imageResizer = new ImageResizer();
        $this->imageResizer->setFileRepository($this->fileRepository);
        parent::setUp();
    }

    #[\Override]
    protected function getExtensions(): array
    {
        $image = $this->fileRepository->createFromLocalFile(
            new FilePointer('inmemory', 'smiley'),
            __DIR__ . '/../Resources/smiley.png',
        );

        return [
            new ImageTwigExtension(),
            new TwigTestExtension([
                'image' => $image,
            ]),
        ];
    }

    #[\Override]
    protected function getRuntimeLoaders()
    {
        return [
            new FactoryRuntimeLoader([
                ImageTwigRuntime::class => function () {
                    return new ImageTwigRuntime($this->imageResizer);
                },
            ]),
        ];
    }

    protected static function getFixturesDirectory(): string
    {
        return __DIR__ . '/Fixtures/';
    }

    protected function getFixturesDir(): string
    {
        return __DIR__ . '/Fixtures/';
    }

    /**
     * @param mixed $file
     * @param mixed $message
     * @param mixed $condition
     * @param mixed $templates
     * @param mixed $exception
     * @param mixed $outputs
     * @param mixed $deprecation
     */
    #[DataProvider('provideIntegrationTests')]
    #[\Override]
    public function testIntegration($file, $message, $condition, $templates, $exception, $outputs, $deprecation = ''): void
    {
        $this->doIntegrationTest($file, $message, $condition, $templates, $exception, $outputs, $deprecation);
    }

    /**
     * @param mixed $file
     * @param mixed $message
     * @param mixed $condition
     * @param mixed $templates
     * @param mixed $exception
     * @param mixed $outputs
     * @param mixed $deprecation
     */
    #[DataProvider('provideLegacyIntegrationTests')]
    #[Group('legacy')]
    #[\Override]
    public function testLegacyIntegration($file, $message, $condition, $templates, $exception, $outputs, $deprecation = ''): void
    {
        $this->doIntegrationTest($file, $message, $condition, $templates, $exception, $outputs, $deprecation);
    }

    /**
     * @return iterable<array-key,array<array-key,mixed>>
     */
    public static function provideIntegrationTests(): iterable
    {
        return self::collectFixtures(false);
    }

    /**
     * @return iterable<array-key,array<array-key,mixed>>
     */
    public static function provideLegacyIntegrationTests(): iterable
    {
        return self::collectFixtures(true);
    }

    /**
     * @return array<array-key,array<array-key,mixed>>
     */
    private static function collectFixtures(bool $legacyTests): array
    {
        $fixturesDir = (string) realpath(static::getFixturesDirectory());
        $tests = [];

        /** @var \SplFileInfo $file */
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($fixturesDir), \RecursiveIteratorIterator::LEAVES_ONLY) as $file) {
            if (!preg_match('/\.test$/', (string) $file)) {
                continue;
            }

            if ($legacyTests xor str_contains($file->getRealpath(), '.legacy.test')) {
                continue;
            }

            $test = (string) file_get_contents($file->getRealpath());

            if (preg_match('/--TEST--\s*(.*?)\s*(?:--CONDITION--\s*(.*))?\s*(?:--DEPRECATION--\s*(.*?))?\s*((?:--TEMPLATE(?:\(.*?\))?--(?:.*?))+)\s*(?:--DATA--\s*(.*))?\s*--EXCEPTION--\s*(.*)/sx', $test, $match)) {
                $message = $match[1];
                $condition = $match[2];
                $deprecation = $match[3];
                $templates = IntegrationTestCase::parseTemplates($match[4]);
                $exception = $match[6];
                $outputs = [[null, $match[5], null, '']];
            } elseif (preg_match('/--TEST--\s*(.*?)\s*(?:--CONDITION--\s*(.*))?\s*(?:--DEPRECATION--\s*(.*?))?\s*((?:--TEMPLATE(?:\(.*?\))?--(?:.*?))+)--DATA--.*?--EXPECT--.*/s', $test, $match)) {
                $message = $match[1];
                $condition = $match[2];
                $deprecation = $match[3];
                $templates = IntegrationTestCase::parseTemplates($match[4]);
                $exception = false;
                preg_match_all('/--DATA--(.*?)(?:--CONFIG--(.*?))?--EXPECT--(.*?)(?=\-\-DATA\-\-|$)/s', $test, $outputs, \PREG_SET_ORDER);
            } else {
                throw new \InvalidArgumentException(\sprintf('Test "%s" is not valid.', str_replace($fixturesDir . '/', '', (string) $file)));
            }

            $tests[str_replace($fixturesDir . '/', '', (string) $file)] = [str_replace($fixturesDir . '/', '', (string) $file), $message, $condition, $templates, $exception, $outputs, $deprecation];
        }

        if ($legacyTests && !$tests) {
            return [['not', '-', '', [], '', []]];
        }

        return $tests;
    }
}
