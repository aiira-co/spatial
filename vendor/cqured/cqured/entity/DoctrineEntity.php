<?php
namespace Cqured\Entity;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;

class DoctrineEntity
{
    private $config;
    private $cache;

    public function __contruct()
    {
        $this->_onInit(new Configuration);
    }

    /**
     * Set default configs for
     * cahce and proxy with production mode.
     *
     * @return void
     */
    private function _onInit($config)
    {
        // get values from config
        include_once '/config.php';
        $enableProdMode = (new Config)->enableProdMode;

        // Set defaults
        if ($enableProdMode) {
            // set default to prod mode
            $cache = new \Doctrine\Common\Cache\ApcCache;
            $config->setAutoGenerateProxyClasses(false);
        } else {
            // set default to dev mode

            $cache = new \Doctrine\Common\Cache\ArrayCache;
            $config->setAutoGenerateProxyClasses(true);
        }
        // Driver Implementation
        // $driverImpl = $config
        //     ->newDefaultAnnotationDriver('/path/to/lib/MyProject/Entities');
        // $config->setMetadataDriverImpl($driverImpl);

        // Cache
        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);

        // Proxies
        $config->setProxyDir('/src/core/domain/');
        $config->setProxyNamespace('Core\Domain');

        $this->config = $config;
        $this->cache = $cache;
    }

    /**
     * Return DOctrine's
     * EntityManager based on the connection string
     *
     * @param [type] $connectionOptions
     * @return void
     */
    public function entityManager(array $connectionOptions)
    {
        return EntityManager::create($connectionOptions, $this->config);
    }

    /**
     * Set Development True/False
     *
     * @param boolean $dev
     * @return self
     */
    public function isDev(bool $dev = false): self
    {
        if ($dev) {
            $this->cache = new \Doctrine\Common\Cache\ArrayCache;
            $this->config->setAutoGenerateProxyClasses(true);
        } else {
            $this->cache = new \Doctrine\Common\Cache\ApcCache;
            $this->config->setAutoGenerateProxyClasses(false);
        }
        return $this;
    }

    // Proxi Directory
    /**
     * Configuration Options
     * The following sections describe all the configuration options
     * available on a Doctrine\ORM\Configuration instance.
     *
     * @param [type] $connectionOptions
     * @return self
     */
    public function setProxyDir(string $dir = '/src/core/domain/media/proxies'): self
    {
        // print_r($this->config);
        // $this->config->setProxyDir($dir);
        return $this;
    }

    /**
     * Gets the directory where Doctrine generates any proxy claseses
     *
     * @param [type] $connectionOptions
     * @return string
     */
    public function getProxyDir(): string
    {
        return $this->config->getProxyDir();
    }
    // Proxy Namespace

    /**
     * Sets the namespace to use for generated proxy classes.
     *
     * @param [type] $connectionOptions
     * @return string
     */
    public function setProxyNamespace($namespace = 'Core\Domain'): self
    {
        $this->config->setProxyNamespace($namespace);
        return $this;
    }

    /**
     * Gets the namespace to use for generated proxy classes.
     *
     * @param [type] $connectionOptions
     * @return string
     */
    public function getProxyNamespace(): string
    {
        return $this->config->getProxyDir();
    }
    /**
     * Sets the metadata driver implementation that is used
     * by Doctrine to acquire the object-relational
     * metadata for your classes
     *
     * @param [type] $connectionOptions
     * @return self
     */
    public function setMetadataDriverImpl($driver): self
    {
        $this->config->setMetadataDriverImpl($driver);
        return $this;
    }
    /**
     * Gets the metadata driver implementation that is used
     * by Doctrine to acquire the object-relational
     * metadata for your classes
     *
     * @param [type] $connectionOptions
     * @return string
     */
    public function getMetadataDriverImpl(): string
    {
        return $this->config->getMetadataDriverImpl();
    }
}
