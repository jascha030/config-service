<?php

declare(strict_types=1);

namespace Jascha030\Config\Tests\Config\Path;

use Exception;
use IteratorAggregate;
use Jascha030\Config\Config\Path\PathIterator;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers \Jascha030\Config\Config\Path\PathIterator
 */
final class PathIteratorTest extends TestCase
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
            new PathIterator(self::$configPath, [])
        );
    }

    /**
     * @throws Exception
     */
    public function testGetIterator(): void
    {
        $paths     = ['path1', 'path2'];
        $aggregate = new PathIterator(self::$configPath, $paths);
        $array     = iterator_to_array($aggregate);

        $this->assertEquals($array, iterator_to_array($aggregate->getIterator()));
        $this->assertEquals(reset($array), sprintf('%s/%s', self::$configPath, $paths[0]));
    }
}
