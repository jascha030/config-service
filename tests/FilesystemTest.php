<?php

declare(strict_types=1);

namespace Jascha030\Config\Tests;

use Jascha030\Config\Config\ConfigDefinitionInterface;
use Jascha030\Config\Config\Path\ConfigIterator;
use Jascha030\Config\Filesystem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\String\Slugger\AsciiSlugger;
use function PHPUnit\Framework\assertDirectoryDoesNotExist;
use function PHPUnit\Framework\assertDirectoryExists;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertInstanceOf;

/**
 * @covers \Jascha030\Config\Filesystem
 *
 * @internal
 */
class FilesystemTest extends TestCase
{
    private static string $baseConfigDir = __DIR__ . '/Fixtures/home/.config';

    public function tearDown(): void
    {
        $dir = sprintf('%s/testApp', self::$baseConfigDir);

        if (is_dir($dir)) {
            $this->deleteTree($dir);
        }
    }

    public function testConstruct(): Filesystem
    {
        $fs = new Filesystem(
            new \Symfony\Component\Filesystem\Filesystem(),
            new AsciiSlugger(),
            self::$baseConfigDir
        );

        assertInstanceOf(Filesystem::class, $fs);

        return $fs;
    }

    /**
     * @depends testConstruct
     */
    public function testCreateFromDefinition(Filesystem $filesystem): void
    {
        $dir = sprintf('%s/testApp', self::$baseConfigDir);

        if (is_dir($dir)) {
            $this->deleteTree($dir);
        }

        assertDirectoryDoesNotExist($dir);

        $filesystem->createFromDefinition($this->getConfigDefinition());

        assertDirectoryExists($dir);
    }

    /**
     * @depends testConstruct
     */
    public function testGetConfigDir(Filesystem $filesystem): void
    {
        assertEquals(self::$baseConfigDir, $filesystem->getConfigDir());
    }

    private function getConfigDefinition(): ConfigDefinitionInterface
    {
        return new class () implements ConfigDefinitionInterface {
            public function getName(): string
            {
                return 'testApp';
            }

            public function getFiles(): ConfigIterator
            {
                $paths = [__DIR__ . '/Fixtures/templates/test.cnf'];
                $files = [];

                foreach ($paths as $path) {
                    $info = new \SplFileInfo($path);

                    $files[$info->getBasename()] = file_get_contents($info->getRealPath());
                }

                return new ConfigIterator($files);
            }
        };
    }

    /**
     * @noinspection PhpReturnValueOfMethodIsNeverUsedInspection
     */
    private function deleteTree(string $dir): bool
    {
        $files = array_diff(scandir($dir), ['.', '..']);

        foreach ($files as $file) {
            $subPath = sprintf('%s/%s', $dir, $file);

            is_dir($subPath)
                ? $this->deleteTree($subPath)
                : unlink($subPath);
        }

        return rmdir($dir);
    }
}
