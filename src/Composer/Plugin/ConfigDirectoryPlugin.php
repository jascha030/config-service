<?php

/**
 * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
 * @noinspection PhpUnused
 */

declare(strict_types=1);

namespace Jascha030\Config\Composer\Plugin;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvent;
use Composer\Installer\PackageEvents;
use Composer\IO\IOInterface;
use Composer\Package\Package;
use Composer\Plugin\PluginInterface;
use Jascha030\Config\Config\ConfigDefinitionInterface;
use Jascha030\Config\Filesystem;
use Symfony\Component\String\Slugger\AsciiSlugger;

class ConfigDirectoryPlugin implements PluginInterface, EventSubscriberInterface
{
    /**
     * {@inheritDoc}
     */
    public function activate(Composer $composer, IOInterface $io): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function deactivate(Composer $composer, IOInterface $io): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function uninstall(Composer $composer, IOInterface $io): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            PackageEvents::POST_PACKAGE_INSTALL => [
                ['onPostPackageInstall', 0],
            ],
        ];
    }

    public function onPostPackageInstall(PackageEvent $event): void
    {
        /**
         * @var Package
         * @noinspection PhpPossiblePolymorphicInvocationInspection
         */
        $package = match ($event->getOperation()->getOperationType()) {
            'install' => $event->getOperation()->getPackage(),
            'update'  => $event->getOperation()->getTargetPackage(),
        };

        $extras = $package->getExtra();

        if (isset($extras['dot-config']) && is_subclass_of($extras['dot-config'], ConfigDefinitionInterface::class)) {
            $configHome = $extras['dot-config-home'] ?? getenv('HOME') . '/.config';
            $definition = new $extras['dot-config']();

            $this->createConfigDir($definition, $configHome);
        }
    }

    private function createConfigDir(ConfigDefinitionInterface $configDefinition, string $configHome): void
    {
        (new Filesystem(new \Symfony\Component\Filesystem\Filesystem(), new AsciiSlugger(), $configHome))
            ->createFromDefinition($configDefinition);
    }
}
