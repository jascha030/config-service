<?php

declare(strict_types=1);

namespace Jascha030\Config\Config\Traits;

use Generator;
use IteratorAggregate;
use Jascha030\Config\Config\Path\ConfigIterator;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

trait TemplateDirectoryTrait
{
    abstract public function getName(): string;

    abstract private function getTemplateDir(): string;

    public function getFiles(): ConfigIterator
    {
        return new ConfigIterator(iterator_to_array($this->getPaths()));
    }

    private function getPaths(): Generator
    {
        foreach ($this->getFinder() as $file) {
            /** @var SplFileInfo $file */
            $path = str_replace(
                $this->getTemplateDir(),
                '',
                $file->getRealPath()
            );

            yield $path => $file->getContents();
        }
    }

    private function getFinder(): IteratorAggregate
    {
        return (Finder::create()
            ->in($this->getTemplateDir())
            ->files()
            ->getIterator())();
    }
}
