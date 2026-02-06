<?php

declare(strict_types=1);

namespace Jascha030\Config\Tests\Config\Path;

use Exception;
use IteratorAggregate;
use Jascha030\Config\Config\Path\ConfigIterator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(ConfigIterator::class)]
final class ConfigIteratorTest extends TestCase
{
    private static array $paths = [
        'path1' => 'content1',
        'path2' => 'content2',
    ];

    public function testConstruct(): void
    {
        $this->assertInstanceOf(IteratorAggregate::class, new ConfigIterator([]));
    }

    /**
     * @throws Exception
     */
    public function testGetIterator(): void
    {
        $aggregate = new ConfigIterator(self::$paths);
        $array     = iterator_to_array($aggregate, true);

        $this->assertEquals($array, iterator_to_array($aggregate->getIterator()));

        foreach (self::$paths as $key => $value) {
            $this->assertArrayHasKey($key, $array);
            $this->assertEquals($array[$key], $value);
        }
    }
}
