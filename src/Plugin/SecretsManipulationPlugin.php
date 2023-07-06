<?php

namespace Kporras07\SecretsManipulationPlugin\Plugin;

use Composer\Plugin\PluginInterface;
use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginEvents;
use Composer\EventDispatcher\Event;

class SecretsManipulationPlugin implements PluginInterface, EventSubscriberInterface
{
    /**
     * @var Composer\Composer;
     */
    private $composer;

    /**
     * @var Composer\IO\IOInterface;
     */
    private $io;

    /**
     * @var array
     */
    private $config;

    /**
     * {@inheritdoc}
     */
    public function activate(Composer $composer, IOInterface $io): void
    {
        $this->composer = $composer;
        $this->io = $io;

        $this->config = $composer->getPackage()->getExtra()['composer-secrets-manipulation-plugin'] ?? [];
    }

    /**
     * {@inheritdoc}
     */
    public function deactivate(Composer $composer, IOInterface $io): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function uninstall(Composer $composer, IOInterface $io): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            PluginEvents::INIT => 'onPluginInit',
        ];
    }

    /**
     * Event handler for post package install.
     */
    public function onPluginInit(Event $event)
    {
        $envVarsMapping = $this->getEnvVarsMapping();
        foreach ($envVarsMapping as $envVar => $newEnvVar) {
            $this->io->write(sprintf('Setting env var %s to %s', $newEnvVar, $envVar));
            $value = getenv($envVar);
            putenv(sprintf('%s=%s', $newEnvVar, $value));
        }
    }

    /**
     * Get env vars mapping array.
     */
    public function getEnvVarsMapping()
    {
        $mapping = [];
        if (isset($this->config['envVarsMapping'])) {
            $mapping = $this->config['envVarsMapping'];
        }
        return $mapping;
    }
}
