<?php
namespace Cqured\Entity;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;

class DoctrineEntity
{
    private $_config;
    private $_cache;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct(string $domain)
    {
        $this->_onInit($domain);
    }

    /**
     * Set default configs for
     * cahce and proxy with production mode.
     *
     * @return void
     */
    private function _onInit(string $domain)
    {
        $config = new Configuration;
        // get values from config
        include_once './config.php';
        $enableProdMode = (new \Config)->enableProdMode;

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
        // echo __DIR__;
        $domainRootPath = './src/core/domain/';

        // I might need to force valye of driver for doamin folder at constructor
        // Driver Implementation
        $driverImpl = $config
            ->newDefaultAnnotationDriver($domainRootPath . $domain);
        $config->setMetadataDriverImpl($driverImpl);

        // Cache
        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);

        // Proxies
        $config->setProxyDir($domainRootPath . $domain . '/proxy');
        $config->setProxyNamespace('Core\Domain\\' . ucfirst($domain));

        $this->_config = $config;
        $this->_cache = $cache;
        // return $this;
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
        return EntityManager::create($connectionOptions, $this->_config);
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
            $this->_cache = new \Doctrine\Common\Cache\ArrayCache;
            $this->_config->setAutoGenerateProxyClasses(true);
        } else {
            $this->_cache = new \Doctrine\Common\Cache\ApcCache;
            $this->_config->setAutoGenerateProxyClasses(false);
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
        // print_r($this->_config);
        // $this->_config->setProxyDir($dir);
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
        return $this->_config->getProxyDir();
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
        $this->_config->setProxyNamespace($namespace);
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
        return $this->_config->getProxyDir();
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
        $this->_config->setMetadataDriverImpl($driver);
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
        return $this->_config->getMetadataDriverImpl();
    }
}
