<?php

namespace Kporras07\SecretsManipulationPlugin\Plugin;

use Composer\Plugin\PluginInterface;
use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginEvents;
use Composer\EventDispatcher\Event;
use Composer\Util\Platform;
use Composer\Config;

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
    private $packageConfig;

    /**
     * @var Composer\Config
     */
    private $composerConfig;

    /**
     * {@inheritdoc}
     */
    public function activate(Composer $composer, IOInterface $io): void
    {
        $this->composer = $composer;
        $this->io = $io;

        $this->composerConfig = $composer->getConfig();

        $this->packageConfig = $composer->getPackage()->getExtra()['composer-secrets-manipulation-plugin'] ?? [];
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
            Platform::putEnv($newEnvVar, $value);
            if ($newEnvVar == "COMPOSER_AUTH") {
                $this->handleComposerAuth();
            }
        }
    }

    protected function handleComposerAuth() {
        if ($composerAuthEnv = Platform::getEnv('COMPOSER_AUTH')) {
            $authData = json_decode($composerAuthEnv);
            if (null === $authData) {
                throw new \UnexpectedValueException('COMPOSER_AUTH environment variable is malformed, should be a valid JSON object');
            } else {
                if ($this->io instanceof IOInterface) {
                    $this->io->writeError('Loading auth config from COMPOSER_AUTH', true, IOInterface::DEBUG);
                }
                $authData = json_decode($composerAuthEnv, true);
                if (null !== $authData) {
                    $this->composerConfig->merge(['config' => $authData], 'COMPOSER_AUTH');
                    $this->composer->setConfig($this->composerConfig);
                }
            }
        }
    }

    /**
     * Get env vars mapping array.
     */
    public function getEnvVarsMapping()
    {
        $mapping = [];
        if (isset($this->packageConfig['envVarsMapping'])) {
            $mapping = $this->packageConfig['envVarsMapping'];
        }
        return $mapping;
    }
}
