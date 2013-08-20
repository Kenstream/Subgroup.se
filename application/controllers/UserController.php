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

class UserController extends SG_Controller_Action
{

    public function init()
    {
        parent::init();
        $this->view->leftMenuIndex = 3;
    }

    public function dashboardAction()
    {

        switch($this->_auth->getIdentity()->role) {
            case Entities\User::TYPE_ADMIN:
                $total = array();

                $total['user'] = sizeof($this->_em->getRepository("Entities\User")->findBy(array('creator' => $this->_auth->getIdentity()->user->getId())));
                $total['project'] = sizeof($this->_em->getRepository("Entities\Project")->findBy(array('user' => $this->_auth->getIdentity()->user->getId())));

                $this->view->total = $total;
                break;
            case Entities\User::TYPE_SUPER_ADMIN:
                $total = array();

                $total['user'] = sizeof($this->_em->getRepository("Entities\User")->findAll());
                $total['project'] = sizeof($this->_em->getRepository("Entities\Project")->findAll());
                $total['scenario'] = sizeof($this->_em->getRepository("Entities\Scenario")->findAll());
                $total['assessment'] = sizeof($this->_em->getRepository("Entities\AssessmentCategory")->findBy(array("type" => NULL)));

                $this->view->total = $total;
                break;
            default:
                $this->view->user = $this->_em->getRepository('Entities\User')
                    ->findOneBy(array('id' => $this->_auth->getIdentity()->user));
                break;

        }

        $this->view->leftMenuIndex = 0;
    }

    public function indexAction()
    {
        $userPaginator = array();
        $pageNumber = $this->getRequest()->getParam('page', 1);
        $pageSize = $this->getRequest()->getParam('size', 100);

        if ($this->_auth->getIdentity()->role == Entities\User::TYPE_ADMIN) {
            $adminUser = $this->_em->getRepository('Entities\User')
                ->findOneBy(array('id' => $this->_auth->getIdentity()->user->getId()));

            $users = $adminUser->getUsers()->toArray();
        } else {
            $users = $this->_em->getRepository('Entities\User')->findAll();
        }

        if (sizeof($users) > 0) {
            $userPaginator = Zend_Paginator::factory($users);
            $userPaginator->setCurrentPageNumber($pageNumber)
                ->setItemCountPerPage($pageSize);
        } else {
            $this->_messages = array(
                array(
                    'text' => $this->_translator->_("You have not create any user yet."),
                    'type' => SG_View_Helper_Alert::TYPE_INFO,
                )
            );
        }

        $this->view->userPaginator = $userPaginator;
    }

    public function createAction()
    {
        $params = $this->getRequest()->getParams();


        $form = new Application_Form_User($this->_em);
        $form->setLegend($this->_translator->_("Create new user"));
        $form->getElement('submit')->setLabel($this->_translator->_("Create user"));

        if ($this->_auth->getIdentity()->role == \Entities\User::TYPE_SUPER_ADMIN) {
            $type = new SG_Form_Element_Select('type');
            $type->addMultiOptions(array(
                "U" => $this->_translator->_("User"),
                "A" => $this->_translator->_("Administrator"),
            ));
            $type->setLabel($this->_translator->_("User Type"));
            $type->setOrder(7);

            $form->addElement($type);

            $accessLimitDate = new SG_Form_Element_Date('accessLimitDate');
            $accessLimitDate->setLabel($this->_translator->_("Access limit date"));
            $accessLimitDate->setOrder(8);

            $form->addElement($accessLimitDate);

            $creator = new SG_Form_Element_Select('creator');
            $creator->setLabel($this->_translator->_("Owner/Administrator of this user"));
            $creator->setOrder(9);

            // get all user with type of administrator
            $administators = $this->_em->getRepository('Entities\User')
                ->findBy(array('type' => Entities\User::TYPE_ADMIN));

            $creator->addMultiOption($this->_auth->getIdentity()->user->getId(), $this->_translator->_("-- Super Administrator --"));

            foreach($administators as $admin) {
                $creator->addMultiOption($admin->getId(), $admin->getFirstName() . ' ' . $admin->getLastName(). ' (' . $admin->getEmail() . ')' );
            }

            $form->addElement($creator);
        }

        if ($this->getRequest()->isPost()) {
            $form->getElement('newPassword')->setRequired(TRUE);
            $form->getElement('confirmPassword')->setRequired(TRUE);

            if ($form->isValid($params)) {
                // check if email has been registered before
                if(!is_null($this->_em->getRepository('Entities\User')->findOneBy(array('email' => $params['email'])))) {
                    $form->getElement('email')->addError($this->_translator->_("This email has been registered before."));
                }

                // if a password entered, it should match the confirmation password
                if (strlen($params['newPassword']) > 0 && $params['newPassword'] != $params['confirmPassword']) {
                    $form->getElement('confirmPassword')->addError($this->_translator->_("The password confirmation does not match"));
                }

                if (sizeof($form->getMessages()) == 0) {
                    $user = new \Entities\User;

                    $user->setEmail($params['email']);
                    $user->setFirstName($params['firstName']);
                    $user->setLastName($params['lastName']);
                    $user->setBirthDate($params['birthDate']);
                    $user->setAccessLimitDate(date('Y-m-d', strtotime("+1 month")));
                    $user->setPassword($params['newPassword']);
                    $user->setType(Entities\User::TYPE_USER);

                    if ($form->getElement('accessLimitDate')) {
                        $user->setAccessLimitDate($params['accessLimitDate']);
                    }

                    if ($form->getElement('type')) {
                        $user->setType($params['type']);
                    }

                    $creatorUser = $this->_em->getRepository('Entities\User')
                        ->findOneBy(array('id' => $this->_auth->getIdentity()->user->getId()));

                    if ($form->getElement('creator') &&
                        $user->getType() == Entities\User::TYPE_USER) {
                        $creatorUser = $this->_em->getRepository('Entities\User')
                            ->findOneBy(array('id' => $params['creator']));
                    }

                    $creatorUser->addUser($user);
                    $this->_em->persist($creatorUser);

                    $this->view->user = $user;
                    try {
                        $this->_em->flush();

                        // set successful flash message
                        $this->_messages[] = array(
                            'type' => SG_View_Helper_Alert::TYPE_SUCCESS,
                            'text' => $this->_translator->_("A new user has been successfully created.")
                        );

                        $form = new Application_Form_User($this->_em);

                    } catch(Exception $e) {
                        // set error on database flash message
                        $this->_messages[] = array(
                            'type' => SG_View_Helper_Alert::TYPE_ERROR,
                            'text' => $this->_translator->_("Server is busy to process your request. Please try again later.: ". $e->getMessage())
                        );
                    }
                }
            }
        }

        $this->view->form = $form;
    }

    public function editAction()
    {

        $params = $this->getRequest()->getParams();
        $this->view->leftMenuIndex = 2;

        // check if the session is owned by an admin, then id can be supplied
        // else it should not have id as parameter and will set id from authenticated user
        if ($this->_auth->getIdentity()->role != Entities\User::TYPE_USER &&
            array_key_exists('id', $params) && (int)$params['id'] > 0) {
            $this->view->leftMenuIndex = 3;
        } else {
            $params['id'] = $this->_auth->getIdentity()->user->getId();
        }

        $queryParams = array('id' => $params['id']);

        if ($this->_auth->getIdentity()->role == Entities\User::TYPE_ADMIN) {
            $queryParams['creator'] = $this->_auth->getIdentity()->user->getId();
        }


        // load a particular user profile for admin view
        $user = $this->_em->getRepository('Entities\User')
            ->findOneBy($queryParams);

        if (is_null($user)) {
            $this->_redirect($this->_helper->RouteUrl('cloud-user-index'));
        }

        // if the given id is found then proceed to process the form with validation
        $form = new Application_Form_User($this->_em, $params);

        $form->getElement('firstName')->setValue($user->getFirstName());
        $form->getElement('lastName')->setValue($user->getLastName());
        $form->getElement('birthDate')->setValue($user->getBirthDate()->format('Y-m-d'));
        $form->getElement('email')->setValue($user->getEmail());

        if ($this->_auth->getIdentity()->role == \Entities\User::TYPE_SUPER_ADMIN &&
            $user->getType() != \Entities\User::TYPE_SUPER_ADMIN) {

            $type = new SG_Form_Element_Select('type');
            $type->addMultiOptions(array(
                "U" => $this->_translator->_("User"),
                "A" => $this->_translator->_("Administrator"),
            ));
            $type->setLabel($this->_translator->_("User Type"));
            $type->setOrder(7);

            $form->addElement($type);

            $accessLimitDate = new SG_Form_Element_Date('accessLimitDate');
            $accessLimitDate->setLabel($this->_translator->_("Access limit date"));
            $accessLimitDate->setOrder(8);

            $form->addElement($accessLimitDate);

            $form->getElement('accessLimitDate')->setValue($user->getAccessLimitDate()->format('Y-m-d'));

            $creator = new SG_Form_Element_Select('creator');
            $creator->setLabel($this->_translator->_("Owner/Administrator of this user"));
            $creator->setOrder(9);

            // get all user with type of administrator
            $administators = $this->_em->getRepository('Entities\User')
                ->findBy(array('type' => Entities\User::TYPE_ADMIN));

            $creator->addMultiOption($this->_auth->getIdentity()->user->getId(), $this->_translator->_("-- Super Administrator --"));
            foreach($administators as $admin) {
                $creator->addMultiOption($admin->getId(), $admin->getFirstName() . ' ' . $admin->getLastName(). ' (' . $admin->getEmail() . ')' );
            }

            $form->addElement($creator);
            $form->getElement('creator')->setValue($user->getCreator()->getId());
        }

        $form->populate($params);

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($params)) {
                // if the email is not the same as previous, check if new email given has already exists or not
                if ($user->getEmail() != $params['email']) {
                    if(!is_null($this->_em->getRepository('Entities\User')->findOneBy(array('email' => $params['email'])))) {
                        $form->getElement('email')->addError($this->_translator->_("This email has been registered before."));
                    }
                }

                // if a password entered, it should match the confirmation password
                if (strlen($params['newPassword']) > 0 && $params['newPassword'] != $params['confirmPassword']) {
                    $form->getElement('confirmPassword')->addError($this->_translator->_("The password confirmation does not match"));
                }


                if (sizeof($form->getMessages()) == 0) {
                    $user->setFirstName($params['firstName']);
                    $user->setLastName($params['lastName']);
                    $user->setBirthDate($params['birthDate']);
                    $user->setEmail($params['email']);

                    if (strlen($params['newPassword']) > 0)
                        $user->setPassword($params['newPassword']);

                    if ($form->getElement('accessLimitDate')) {
                        $user->setAccessLimitDate($params['accessLimitDate']);
                    }

                    if ($form->getElement('type')) {
                        $user->setType($params['type']);
                    }


                    if ($form->getElement('creator') &&
                        $user->getType() == Entities\User::TYPE_USER &&
                        $user->getCreator()->getId() != $params['creator']) {

                        $oldCreator = $user->getCreator();
                        $oldCreator->getUsers()->removeElement($user);
                        $this->_em->persist($oldCreator);

                        $creatorUser = $this->_em->getRepository('Entities\User')
                            ->findOneBy(array('id' => $params['creator']));

                        $creatorUser->addUser($user);
                        $this->_em->persist($creatorUser);

                    } else if ($form->getElement('creator') &&
                        $user->getType() == Entities\User::TYPE_ADMIN) {
                        $oldCreator = $user->getCreator();
                        $oldCreator->getUsers()->removeElement($user);
                        $this->_em->persist($oldCreator);

                        $creatorUser = $this->_em->getRepository('Entities\User')
                            ->findOneBy(array('id' => $this->_auth->getIdentity()->user->getId()));

                        $creatorUser->addUser($user);
                        $this->_em->persist($creatorUser);
                    } else {
                        $this->_em->persist($user);
                    }

                    try {
                        $this->_em->flush();

                        // set successful flash message
                        $this->_messages[] = array(
                            'type' => SG_View_Helper_Alert::TYPE_SUCCESS,
                            'text' => $this->_translator->_("User profile information has been successfully updated.")
                        );

                    } catch (Exception $e) {
                        // set error on database flash message
                        $this->_messages[] = array(
                            'type' => SG_View_Helper_Alert::TYPE_ERROR,
                            'text' => $this->_translator->_("Server is busy to process your request. Please try again later.")
                        );
                    }
                }

            }
        }

        $this->view->form = $form;
    }

    public function detailAction()
    {
        $params = $this->getRequest()->getParams();

        switch($this->_auth->getIdentity()->role) {
            case Entities\User::TYPE_ADMIN:
                if (array_key_exists('id', $params) && is_numeric($params['id'])) {
                    $user = $this->_em->getRepository('Entities\User')
                        ->findOneBy(array('id' => $params['id']));

                    if (!is_null($user)) {
                        $this->view->user = $user;
                    } else {
                        $this->_redirect($this->_helper->RouteUrl('cloud-user-index'));
                    }
                } else {
                    $this->_redirect($this->_helper->RouteUrl('cloud-user-index'));
                }
                break;
            default:
                $this->view->user = $this->_em->getRepository('Entities\User')
                    ->findOneBy(array('id' => $this->_auth->getIdentity()->user));
                break;
        }

        $this->view->em = $this->_em;
    }

    public function resultAction()
    {
        $user = $this->_em->getRepository('Entities\User')
            ->findOneBy(array('id' => $this->_auth->getIdentity()->user->getId()));

        $this->view->invitations = $user->getInvitations();
        $this->view->leftMenuIndex = 1;
    }

    public function deleteAction()
    {
        // set the controller not to render any view
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $params = $this->getRequest()->getParams();

        if (array_key_exists('id', $params) && is_numeric($params['id'])) {
            $user = $this->_em->getRepository('Entities\User')
                ->findOneBy(array('id' => $params['id']));

            if(!is_null($user)) {
                try {
                    $this->_em->remove($user);
                    $this->_em->flush();

                    $this->_helper->FlashMessenger->addMessage($this->_translator->_("The user has been deleted."));
                } catch (Exception $e) {
                    $this->_helper->FlashMessenger->addMessage($this->_translator->_("Server is busy, please delete the user later."));
                }
            }
        }

        $this->_redirect($this->_helper->RouteUrl('cloud-user-index'));
    }

}

