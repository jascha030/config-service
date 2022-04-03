<?php

declare(strict_types=1);

namespace Jascha030\Config\Config\Path;

use Traversable;

class PathIterator implements \IteratorAggregate
{
    private \Closure $factory;

    public function __construct(string $directory, array $paths)
    {
        $this->factory = (static function () use ($directory, $paths) {
            foreach ($paths as $name => $path) {
                yield $directory . '/' . $path;
            }
        })(...);
    }

    public function getIterator(): Traversable
    {
        return yield from ($this->factory)();
    }
}
