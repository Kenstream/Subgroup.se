<?php

/**
 * A plugin class for access control list
 *
 * @category   Subgroup
 * @package    SG_Controller_Plugin
 * @author     Agustinus Prasetyo Widodo <agustinus.widodo@gmail.com>
 * @copyright  2013 Agustinus Prasetyo
 * @version    SVN: $Id$
 */

class SG_Controller_Plugin_AclManager extends Zend_Controller_Plugin_Abstract
{

    // the action to dispatch if a user doesn't have sufficient privileges
    private $_authController = array('controller' => 'index', 'action' => 'index');
    private $auth;
    private $acl;

    public function __construct(Zend_Auth $auth)
    {
        $this->auth = $auth;
        $this->acl = new Zend_Acl();

        // add the different user roles
        $this->acl->addRole(new Zend_Acl_Role(Entities\User::TYPE_GUEST));
        $this->acl->addRole(new Zend_Acl_Role(Entities\User::TYPE_USER));
        $this->acl->addRole(new Zend_Acl_Role(Entities\User::TYPE_ADMIN),Entities\User::TYPE_USER);
        $this->acl->addRole(new Zend_Acl_Role(Entities\User::TYPE_SUPER_ADMIN),Entities\User::TYPE_ADMIN);

        // add the resources we want to have control over
        $this->acl->addResource(new Zend_Acl_Resource('ajax'));
        $this->acl->addResource(new Zend_Acl_Resource('assessment'));
        $this->acl->addResource(new Zend_Acl_Resource('error'));
        $this->acl->addResource(new Zend_Acl_Resource('form'));
        $this->acl->addResource(new Zend_Acl_Resource('index'));
        $this->acl->addResource(new Zend_Acl_Resource('menu'));
        $this->acl->addResource(new Zend_Acl_Resource('notification'));
        $this->acl->addResource(new Zend_Acl_Resource('project'));
        $this->acl->addResource(new Zend_Acl_Resource('result'));
        $this->acl->addResource(new Zend_Acl_Resource('scenario'));
        $this->acl->addResource(new Zend_Acl_Resource('user'));

        // allow access to everything for all users by default
        // except for the account management and administration areas
        $this->acl->allow();
        $this->acl->deny(null, 'ajax');
        $this->acl->deny(null, 'assessment');
        $this->acl->deny(null, 'error');
        $this->acl->deny(null, 'form');
        $this->acl->deny(null, 'index');
        $this->acl->deny(null, 'menu');
        $this->acl->deny(null, 'notification');
        $this->acl->deny(null, 'project');
        $this->acl->deny(null, 'result');
        $this->acl->deny(null, 'scenario');
        $this->acl->deny(null, 'user');

        // add an exception so guests can log in or register
        // in order to gain privilege
        // and to browse around to unauthenticated area

        $this->acl->allow(Entities\User::TYPE_GUEST, 'index', array('index', 'change-language', 'forget-password'));
        $this->acl->allow(Entities\User::TYPE_GUEST, 'error');

        // add an exception to members so they can
        // browse around to authenticated area and some unauthenticated area
        $this->acl->allow(Entities\User::TYPE_USER, 'ajax');
        $this->acl->allow(Entities\User::TYPE_USER, 'error');
        $this->acl->allow(Entities\User::TYPE_USER, 'form');
        $this->acl->allow(Entities\User::TYPE_USER, 'index', array('logout', 'change-language'));
        $this->acl->allow(Entities\User::TYPE_USER, 'menu');
        $this->acl->allow(Entities\User::TYPE_USER, 'project', array('chart-detail'));
        $this->acl->allow(Entities\User::TYPE_USER, 'result');
        $this->acl->allow(Entities\User::TYPE_USER, 'user', array('dashboard', 'result', 'detail', 'edit'));

        $this->acl->allow(Entities\User::TYPE_ADMIN, 'notification');
        $this->acl->allow(Entities\User::TYPE_ADMIN, 'project');
        $this->acl->allow(Entities\User::TYPE_ADMIN, 'user');

        $this->acl->allow(Entities\User::TYPE_SUPER_ADMIN, 'assessment');
        $this->acl->allow(Entities\User::TYPE_SUPER_ADMIN, 'scenario');

    }

    /**
    * preDispatch
    *
    * Before an action is dispatched, check if the current user
    * has sufficient privileges. If not, dispatch the default
    * action instead
    *
    * @param Zend_Controller_Request_Abstract $request
    */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        // check if a user is logged in and has a valid role,
        // otherwise, assign them the default role (guest)
        if ($this->auth->hasIdentity())
            $role = $this->auth->getIdentity()->role;
        else
            $role = Entities\User::TYPE_GUEST;

        if(!$this->acl->hasRole($role))
            $role = Entities\User::TYPE_GUEST;

        // the ACL resource is the requested controller name
        $resource = $request->controller;

        // the ACL privilege is the requested action name
        $privilege = $request->action;

        // if we haven't explicitly added the resource, check
        // the default global permissions
        if (!$this->acl->has($resource))
            $resource = null;

        // access denied - reroute the request to the default action handler
        if (!$this->acl->isAllowed($role, $resource, $privilege)) {
            $request->setControllerName($this->_authController['controller']);
            $request->setActionName($this->_authController['action']);
        }
    }
}

