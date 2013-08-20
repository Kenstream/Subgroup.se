<?php

/**
 * A class which act as a manager/driver between Zend And Doctrine
 *
 * @category   Subgroup
 * @package    SG_Resource
 * @author     Agustinus Prasetyo Widodo <agustinus.widodo@gmail.com>
 * @copyright  2011-2013 Agustinus Prasetyo
 * @version    SVN: $Id$
 */

class SG_Resource_Entitymanager extends Zend_Application_Resource_ResourceAbstract
{

    public function init()
    {

      // Custom resource plugins inherit this sweet getOptions() method which will retrieve
      // configuration settings from the application.ini file
      $config = new Zend_Config($this->getOptions());

      // Define the connection parameters
      $options = array(
          'connection' => array(
          'driver'   => "{$config->connection->driver}",
          'host'     => "{$config->connection->host}",
          'dbname'   => "{$config->connection->dbname}",
          'user'     => "{$config->connection->user}",
          'password' => "{$config->connection->password}",
          'charset'  => "{$config->connection->charset}",
          'driverOptions' => array( 1002 => "SET NAMES utf8"),
        )
      );

      $configEm = new \Doctrine\ORM\Configuration;

      $cache = null;
      if ($this->getBootstrap()->getEnvironment() == "production") {
        $cache = new \Doctrine\Common\Cache\ApcCache;
        //$cache->deleteAll();
      } else {
        $cache = new \Doctrine\Common\Cache\ArrayCache;
      }

      $driverImpl = $configEm->newDefaultAnnotationDriver(
        $config->connection->entities
      );

      $configEm->setMetadataCacheImpl($cache);

      $configEm->setMetadataDriverImpl($driverImpl);

      // Configure proxies

      $configEm->setAutoGenerateProxyClasses(
        $config->connection->proxies->generate
      );

      $configEm->setProxyNamespace($config->connection->proxies->ns);

      $configEm->setProxyDir(
        $config->connection->proxies->location
      );

      // Configure cache

      $configEm->setQueryCacheImpl($cache);

      $em = \Doctrine\ORM\EntityManager::create($options['connection'], $configEm);
      Zend_Registry::set('em', $em);

      return $em;

    }

}