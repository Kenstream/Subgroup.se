<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initHttpsRedirection()
    {
        if($_SERVER['SERVER_PORT'] != 443) {
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
            exit();
        }
    }

    protected function _initConfig()
    {
        $this->bootstrap('Frontcontroller');

        $config = new Zend_Config($this->getOptions());
        Zend_Registry::set('config', $config);

        $setting = new Zend_Config_Ini(APPLICATION_PATH . '/configs/setting.ini', APPLICATION_ENV);
        Zend_Registry::set('setting', $setting);

        Zend_Registry::set('environment', $this->getEnvironment());
    }

    protected function _initDoctrine()
    {
        require_once('Doctrine/Common/ClassLoader.php');

        $autoloader = Zend_Loader_Autoloader::getInstance();

        // Load the entities
        $classLoader = new \Doctrine\Common\ClassLoader('Entities',
        realpath(Zend_Registry::get('config')->resources->entityManager->connection->entities), 'loadClass');
            $autoloader->pushAutoloader(array($classLoader, 'loadClass'), 'Entities');

        // Load the repositories
        $classLoader = new \Doctrine\Common\ClassLoader('Repositories',
        realpath(Zend_Registry::get('config')->resources->entityManager->connection->entities), 'loadClass');

        $autoloader->pushAutoloader(array($classLoader, 'loadClass'), 'Repositories');
    }

    protected function _initLocale()
    {
        date_default_timezone_set(Zend_Registry::get('setting')->default->timezone);
        $em = $this->getResource('entityManager');

        // set default Zend_Locale
        $locale = null;
        $localeSession = new Zend_Session_Namespace('localeSession');

        if (isset($localeSession->default)) {
            $locale = $localeSession->default;
        } else {
            $locale = new Zend_Locale(Zend_Registry::get('setting')->default->locale);
            $localeSession->default = $locale;
        }

        Zend_Registry::set('Zend_Locale', $locale);

        // set default Zend_Translate
        $translate = new Zend_Translate('gettext',
            APPLICATION_BASE_PATH . '/data/languages/',
            Zend_Registry::get('Zend_Locale')->getLanguage(),
            array('scan' => Zend_Translate::LOCALE_FILENAME)
        );

        Zend_Registry::set('Zend_Translate', $translate);
    }

    protected function _initRoutes()
    {
        $router = new Zend_Controller_Router_Rewrite();
        $routeConfigXml = new Zend_Config_Xml(APPLICATION_PATH . '/routes/'.
                                Zend_Registry::get('Zend_Locale')->getLanguage() .'.xml');

        $router->removeDefaultRoutes();
        $router->addConfig($routeConfigXml);
        $this->frontcontroller->setRouter($router);
    }

    protected function _initAcl()
    {
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session());

        // register authentication manager controller
        $this->frontController->registerPlugin(new SG_Controller_Plugin_AclManager($auth));
    }

    protected function _initMail()
    {
        $setting = Zend_Registry::get('setting');
        $config = array(
            'ssl' => $setting->email->encryption,
            'port' => $setting->email->port,
            'auth' => $setting->email->auth,
            'username' => $setting->email->address,
            'password' => $setting->email->password
        );

        $smtpConnection = new Zend_Mail_Transport_Smtp($setting->email->smtp, $config);

        Zend_Mail::setDefaultFrom($setting->email->address, $setting->email->from);

        Zend_Registry::set('mailTransport', $smtpConnection);
    }

}

