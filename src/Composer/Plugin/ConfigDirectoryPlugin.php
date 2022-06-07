<?php

/**
 * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
 * @noinspection PhpUnused
 */

declare(strict_types=1);

namespace Jascha030\Config\Composer\Plugin;

if (is_file(__DIR__ . '/../../../autoload.php')) {
    /** @noinspection PhpIncludeInspection */
    require __DIR__ . '/../../../autoload.php';
}

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Package\BasePackage;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use Jascha030\Config\Config\ConfigDefinitionInterface;
use Jascha030\Config\Filesystem;
use Symfony\Component\String\Slugger\AsciiSlugger;

class ConfigDirectoryPlugin implements PluginInterface, EventSubscriberInterface
{
    private array $packageQueue = [];

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
        $callback = ['packageConfigHandler', 0];

        return [
            ScriptEvents::POST_INSTALL_CMD        => [$callback],
            ScriptEvents::POST_UPDATE_CMD         => [$callback],
            ScriptEvents::POST_CREATE_PROJECT_CMD => [$callback],
            ScriptEvents::POST_AUTOLOAD_DUMP      => [],
        ];
    }

    public function packageConfigHandler(Event $event): void
    {
        $root = $event->getComposer()->getPackage()->getName();

        if ('__root__' !== $root) {
            return;
        }

        $packages = $event
            ->getComposer()
            ->getRepositoryManager()
            ->getLocalRepository()
            ->getPackages();

        foreach ($packages as $package) {
            $this->addPackage($package);
        }
    }

    private function addPackage(BasePackage $package): void
    {
        $extra = $package->getExtra();

        if (! isset($extra['dot-config']) || isset($this->packageQueue[$package->getName()])) {
            return;
        }

        $this->packageQueue[$package->getName()] = [
            'class' => $extra['dot-config'],
            'root'  => $extra['dot-config-root'] ?? null,
        ];
    }

    private function createConfigDir(ConfigDefinitionInterface $configDefinition, string $configHome): void
    {
        (new Filesystem(new \Symfony\Component\Filesystem\Filesystem(), new AsciiSlugger(), $configHome))
            ->createFromDefinition($configDefinition);
    }

    private function handlePackage(BasePackage $package): void
    {
        $extra = $package->getExtra();

        if (! isset($extra['dot-config'])) {
            return;
        }

        if (! is_subclass_of($extra['dot-config'], ConfigDefinitionInterface::class)) {
            echo $extra['dot-config'];

            return;
        }

        var_dump($package->getName());

        exit();

        $definition    = new $extra['dot-config']();
        $rootConfigDir = $extra['dot-config-root'] ?? null;

        $this->createConfigDir($definition, $rootConfigDir);
    }
}
