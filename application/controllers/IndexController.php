<?php

/**
 * Controller that handles User management.
 *
 * This controller handles the user management area of the portal
 *
 * @category   Subgroup
 * @package    Controller
 * @author     Agustinus Prasetyo Widodo <agustinus.widodo@gmail.com>
 * @copyright  2013 Agustinus Prasetyo
 * @version    SVN: $Id$
 * @link       https://cloud.subgroup.se
 */

class IndexController extends SG_Controller_Action
{

    public function init()
    {
        parent::init();
        $this->_helper->layout->disableLayout();
        //$this->_helper->SendMail('pras.oei@gmail.com', 'Agustinus', "whatever..", "send-user-credential.phtml");
    }

    /**
     * Handles login/authentication for user
     */
    public function indexAction()
    {
        $form = new Application_Form_Login();

        $redirect = $this->getRequest()->getParam('_redirect',
            $this->_helper->RouteUrl('cloud-dashboard', $this->getRequest()->getParams()));

        if ($this->_auth->hasIdentity())
            $this->_redirect($redirect);

        $form->setAction($this->_helper->RouteUrl('cloud-index', $this->getRequest()->getParams()));

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getParams())) {

                // create auth dp adapter
                $authAdapter = new Zend_Auth_Adapter_DbTable(
                    Zend_Db_Table_Abstract::getDefaultAdapter(),
                    'users', 'email', 'password', 'SHA1(CONCAT(?, salt))');

                $authAdapter->setIdentity($form->getValue('email'));
                $authAdapter->setCredential($form->getValue('password'));

                $result = $this->_auth->authenticate($authAdapter);

                if ($result->isValid()) {
                    $user = $this->_em->getRepository('Entities\User')
                        ->findOneById($authAdapter->getResultRowObject()->id);

                    if (strtotime($user->getAccessLimitDate()->format('Y-m-d')) > time()) {
                        $identity = new stdClass;
                        $identity->user = $user;
                        $identity->role = $user->getType();

                        $this->_auth->getStorage()->write($identity);

                        // if user exists send them to their home page
                        $this->_helper->FlashMessenger->addMessage($this->_translator->_("You are logged in."));
                        $this->_redirect($redirect);
                    } else {
                        $this->_auth->clearIdentity();
                        $form->getElement('email')->addError($this->_translator->_("This email has passed its access limit time. Please contact admin@subgroup.se for extension of access period."));
                    }
                } else {
                    $form->getElement('password')->addError($this->_translator->_("Invalid username or password."));
                }
            }
        }

        $this->view->redirect   = $redirect;
        $this->view->form       = $form;
    }

    /**
     * Handles changing the panel language
     */
    public function changeLanguageAction()
    {

    }

    /**
     * Handles the password recovery for the user
     */
    public function forgetPasswordAction()
    {

    }

    /**
     * Handles the logging off from the system
     */
    public function logoutAction()
    {
        $this->_auth->clearIdentity();
        $this->_redirect($this->_helper->RouteUrl('cloud-index', $this->getRequest()->getParams()));
    }

}

