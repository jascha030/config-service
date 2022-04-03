<?php

declare(strict_types=1);

namespace Jascha030\Config\Config;

use Jascha030\Config\Config\Path\PathIterator;

interface ConfigDefinitionInterface
{
    public function getName(): string;

    public function getPaths(): PathIterator;
}
