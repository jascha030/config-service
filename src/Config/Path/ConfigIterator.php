<?php

declare(strict_types=1);

namespace Jascha030\Config\Config\Path;

use Closure;
use Generator;
use IteratorAggregate;
use Traversable;

class ConfigIterator implements IteratorAggregate
{
    private Closure $factory;

    public function __construct(iterable $paths)
    {
        $this->factory = static fn (): Generator => yield from $paths;
    }

    public function getIterator(): Traversable
    {
        return yield from ($this->factory)();
    }
}
