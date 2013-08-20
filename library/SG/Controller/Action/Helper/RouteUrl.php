<?php

/**
* Create the URL string from loaded routing configuration
*
* @param        string  $route     the route's name/identifier in the routing configuration
* @param        array   $params    the parameters supplied to the route
*
* @category   Subgroup
* @package    SG_Controller_Action_Helper
* @author     Agustinus Prasetyo Widodo <agustinus.widodo@gmail.com>
* @copyright  2013 Agustinus Prasetyo
* @version    SVN: $Id$
*/

class SG_Controller_Action_Helper_RouteUrl extends Zend_Controller_Action_Helper_Abstract
{
  public function direct($route, array $params = array())
  {
    $navigationPageHelper = new Zend_Navigation_Page_Mvc();
    $navigationPageHelper->setRoute($route);
    $navigationPageHelper->setParams($params);

    return $navigationPageHelper->getHref();
  }
}
