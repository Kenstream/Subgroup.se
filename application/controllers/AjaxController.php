<?php

/**
 * Controller that handles Ajax management.
 *
 * This controller handles the ajax request of the portal
 *
 * @category   Subgroup
 * @package    Controller
 * @author     Agustinus Prasetyo Widodo <agustinus.widodo@gmail.com>
 * @copyright  2013 Agustinus Prasetyo
 * @version    SVN: $Id$
 * @link       https://cloud.subgroup.se
 */

class AjaxController extends SG_Controller_Action
{
    protected $_responseData;

    public function init()
    {
        parent::init();

        // check if function exists
        $params = $this->getRequest()->getParams();

        if (!method_exists($this, $this->dashesToCamelCase($params['action']) . 'Action')) {
            $this->_responseData = array(
                'code' => 400,
                'status' => 'Invalid_AJAX_Method',
                'data' => "Invalid AJAX method name"
            );

            $this->getResponse()->setHttpResponseCode($this->_responseData['code']);
            $this->_helper->json->sendJson($this->_responseData);
            exit;
        }

        if (!$this->getRequest()->isXmlHttpRequest()) {
              $this->_responseData = array(
                'code' => 400,
                'status' => 'Invalid_HTTP_Request',
                'data' => array('messages' => "Invalid format AJAX HTTP request.")
            );

            $this->getResponse()->setHttpResponseCode($this->_responseData['code']);
            $this->_helper->json->sendJson($this->_responseData);
            exit;
        }
    }

    public function postDispatch()
    {
        parent::postDispatch();

        $this->getResponse()->setHttpResponseCode($this->_responseData['code']);
        $this->_helper->json->sendJson($this->_responseData);
    }

    public function getEmailListAction()
    {
        $emailExceptionList = array();
        $exceptionListString = trim($this->getRequest()->getParam('exceptEmail', ''));

        if(strlen(trim($this->getRequest()->getParam('exceptEmail', ''))) > 0)
            $emailExceptionList = explode(';', $exceptionListString);

        $emailSearch = $this->getRequest()->getParam('searchEmail', '');

        $owner = $this->_em->getRepository('Entities\User')
            ->findOneBy(array('id' => $this->getRequest()->getParam('owner')));

        $ownerToSearch = null;
        if ($owner && $owner->getType() == Entities\User::TYPE_ADMIN) {
            $ownerToSearch = $owner;
        }

        try {
            $userEmails = $this->_em->getRepository('Entities\User')->searchEmails($emailSearch, $emailExceptionList, $ownerToSearch);
            $this->_responseData = array(
                'code' => 200,
                'status' => 'OK',
                'data' => array('emails' => $userEmails)
            );
        } catch (Exception $e) {
            $this->_responseData = array(
                'code' => 400,
                'status' => 'Error_Serving_Request',
                'data' => array('messages' => 'Server error for given request. ' . $e->getMessage())
            );
        }
    }

    public function checkEmailExistAction()
    {
        $emailToCheck = $this->getRequest()->getParam('email', null);

        if (!is_null($emailToCheck)) {
            $emailFound = $this->_em->getRepository('Entities\User')
                ->findOneBy(array('email' => $emailToCheck));

            if ($emailFound) {
                $this->_responseData = array(
                    'code' => 200,
                    'status' => 'OK',
                    'data' => array('exist' => true)
                );
            } else {
                $this->_responseData = array(
                    'code' => 200,
                    'status' => 'OK',
                    'data' => array('exist' => false)
                );
            }
        } else {
            $this->_responseData = array(
                'code' => 400,
                'status' => 'Error_Serving_Request',
                'data' => array('messages' => 'Server error for given request.')
            );
        }
    }

    protected function dashesToCamelCase($string, $capitalizeFirstCharacter = false)
    {

        $str = str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));

        if (!$capitalizeFirstCharacter) {
            $str[0] = strtolower($str[0]);
        }

        return $str;
    }
}