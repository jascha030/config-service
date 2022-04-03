<?php

declare(strict_types=1);

namespace Jascha030\Config\Config;

interface ConfigDefinitionInterface
{
    public function getName(): string;

    public function getPaths(): array;
}
