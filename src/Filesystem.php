<?php

declare(strict_types=1);

namespace Jascha030\Config;

use Jascha030\Config\Config\ConfigDefinitionInterface;
use Symfony\Component\Filesystem\Filesystem as BaseFilesystem;
use Symfony\Component\String\Slugger\SluggerInterface;

class Filesystem
{
    private string $baseConfigDir;

    public function __construct(
        private BaseFilesystem $filesystem,
        private SluggerInterface $slugger,
        ?string $baseConfigDir = null,
    ) {
        $this->baseConfigDir = $baseConfigDir ?? getenv('HOME') . '/.config';

        if (! $this->filesystem->exists($this->baseConfigDir)) {
            throw new \InvalidArgumentException('Invalid dir.');
        }
    }

    public function createFromDefinition(ConfigDefinitionInterface $definition): void
    {
        $directoryPath = sprintf('%s/%s', $this->getConfigDir(), $this->slugger->slug($definition->getName()));

        if (! $this->filesystem->exists($directoryPath)) {
            $this->filesystem->mkdir($directoryPath);
        }

        foreach ($definition->getPaths() as $name => $path) {


            if (! $this->filesystem->exists($path)) {
                $this->filesystem->touch($path);
            }
        }
    }

    public function getConfigDir(): string
    {
        return $this->baseConfigDir;
    }
}
