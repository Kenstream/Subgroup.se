<?php

/**
* Create the URL string from loaded routing configuration
*
* @param        string  $route     the route's name/identifier in the routing configuration
* @param        array   $params    the parameters supplied to the route
*
* @category   Subgroup
* @package    SG_View_Helper
* @author     Agustinus Prasetyo Widodo <agustinus.widodo@gmail.com>
* @copyright  2013 Agustinus Prasetyo
* @version    SVN: $Id$
*/

class SG_View_Helper_RouteUrl extends Zend_View_Helper_Abstract
{
    public function routeUrl($routeName, $params = array())
    {
        $navigationPageHelper = new Zend_Navigation_Page_Mvc();
        $navigationPageHelper->setRoute($routeName);
        $navigationPageHelper->setParams($params);

        return $navigationPageHelper->getHref();
    }
}