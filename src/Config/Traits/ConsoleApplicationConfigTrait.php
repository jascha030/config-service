<?php

declare(strict_types=1);

namespace Jascha030\Config\Config\Traits;

use Symfony\Component\Console\Application;

trait ConsoleApplicationConfigTrait
{
    abstract private function getApplication(): Application;

    public function getName(): string
    {
        return $this->getApplication()->getName();
    }
}
