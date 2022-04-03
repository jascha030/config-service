<?php

declare(strict_types=1);

namespace Jascha030\Config\Tests\Config\Path;

use Exception;
use IteratorAggregate;
use Jascha030\Config\Config\Path\ConfigIterator;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers \Jascha030\Config\Config\Path\ConfigIterator
 */
final class ConfigIteratorTest extends TestCase
{
    public static ?string $configPath = null;

    public static function setUpBeforeClass(): void
    {
        self::$configPath = sprintf('%s/.config', getenv('HOME'));
    }

    public static function tearDownAfterClass(): void
    {
        self::$configPath = null;
    }

    public function testConstruct(): void
    {
        $this->assertInstanceOf(
            IteratorAggregate::class,
            new ConfigIterator(self::$configPath, [])
        );
    }

    /**
     * @throws Exception
     */
    public function testGetIterator(): void
    {
        $paths     = ['path1' => 'content1', 'path2' => 'content2'];
        $aggregate = new ConfigIterator(self::$configPath, $paths);
        $array     = iterator_to_array($aggregate, true);

        $this->assertEquals($array, iterator_to_array($aggregate->getIterator()));

        foreach ($paths as $key => $value) {
            $path = sprintf('%s/%s', self::$configPath, $key);
            $this->assertArrayHasKey($path, $array);
            $this->assertEquals($array[$path], $value);
        }
    }
}
