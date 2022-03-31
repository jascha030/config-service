<?php

declare(strict_types=1);

namespace Jascha030\Config\Config;

interface ConfigDefinitionInterface
{
    public function getName();

    public function getFiles();
}
