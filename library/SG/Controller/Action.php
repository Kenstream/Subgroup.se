<?php

class SG_Controller_Action extends Zend_Controller_Action
{
    protected $_em;
    protected $_auth;
    protected $_messages;
    protected $_translator;

    public function init()
    {
        parent::init();

        // init empty array for messages
        $this->_messages = array();
        $this->view->params = $this->getRequest()->getParams();
    }

    public function preDispatch()
    {
        // init all parameter required by all controllers such as database connection, translation
        $this->_em = $this->getInvokeArg('bootstrap')->getResource('entityManager');
        $this->_auth = Zend_Auth::getInstance();
        $this->_translator = Zend_Registry::get('Zend_Translate');

    }

    public function postDispatch()
    {
        // init all parameters required by view helpers
        $this->view->isAuthenticated = ($this->_auth->hasIdentity()) ? TRUE : FALSE;
        $this->view->identity = ($this->_auth->hasIdentity()) ? $this->_auth->getIdentity() : null;
        $this->view->messages = $this->_messages;
        $this->view->flashMessages = $this->_helper->FlashMessenger->getMessages();
    }

}

