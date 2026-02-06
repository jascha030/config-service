<?php

declare(strict_types=1);

namespace Jascha030\Config;

use InvalidArgumentException;
use Jascha030\Config\Config\ConfigDefinitionInterface;
use Symfony\Component\Filesystem\Filesystem as BaseFilesystem;
use Symfony\Component\String\Slugger\SluggerInterface;

use function sprintf;

class Filesystem
{
    private string $baseConfigDir;

    public function __construct(
        private BaseFilesystem $filesystem,
        private SluggerInterface $slugger,
        ?string $baseConfigDir,
    ) {
        $this->baseConfigDir = $baseConfigDir;

        if (! $this->filesystem->exists($this->baseConfigDir)) {
            throw new InvalidArgumentException("Invalid directory: \"{$this->baseConfigDir}\".");
        }
    }

    public function createFromDefinition(ConfigDefinitionInterface $definition): void
    {
        $directoryPath = sprintf(
            '%s/%s',
            $this->getConfigDir(),
            $this->slugger->slug($definition->getName())->toString()
        );

        if (! $this->filesystem->exists($directoryPath)) {
            $this->filesystem->mkdir($directoryPath);
        }

        foreach ($definition->getFiles() as $path => $contents) {
            $path = sprintf('%s/%s', $directoryPath, $path);

            if (! $this->filesystem->exists($path)) {
                $this->filesystem->touch($path);
                $this->filesystem->dumpFile($path, $contents);
            }
        }
    }

    public function getConfigDir(): string
    {
        return $this->baseConfigDir;
    }
}
