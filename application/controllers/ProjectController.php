<?php

/**
 * Controller that handles Project menu.
 *
 * This controller handles the project left menu area of the portal
 *
 * @category   Subgroup
 * @package    Controller
 * @author     Agustinus Prasetyo Widodo <agustinus.widodo@gmail.com>
 * @copyright  2013 Agustinus Prasetyo
 * @version    SVN: $Id$
 * @link       https://cloud.subgroup.se
 */

class ProjectController extends SG_Controller_Action
{
    public function init()
    {
        parent::init();
        $this->view->leftMenuIndex = 4;
    }

    public function indexAction()
    {
        $projectPaginator = array();
        $pageNumber = $this->getRequest()->getParam('page', 1);
        $pageSize = $this->getRequest()->getParam('size', 20);

        if ($this->_auth->getIdentity()->role == Entities\User::TYPE_ADMIN) {
            $adminUser = $this->_em->getRepository('Entities\User')
                ->findOneBy(array('id' => $this->_auth->getIdentity()->user->getId()));

            $projects = $adminUser->getProjects()->toArray();
        } else {
            $projects = $this->_em->getRepository('Entities\Project')->findAll();
        }

        if (sizeof($projects) > 0) {
            $projectPaginator = Zend_Paginator::factory($projects);
            $projectPaginator->setCurrentPageNumber($pageNumber)
                ->setItemCountPerPage($pageSize);
        } else {
            $this->_messages = array(
                array(
                    'text' => $this->_translator->_("You have not create any project yet."),
                    'type' => SG_View_Helper_Alert::TYPE_INFO,
                )
            );
        }

        $this->view->projectPaginator = $projectPaginator;
        $this->view->projects = $projects;
    }

    public function createAction()
    {
        $params = $this->getRequest()->getParams();
        $form = new Application_Form_Project($this->_em);

        $form->getElement('owner')->setValue($this->_auth->getIdentity()->user->getId());

        if ($this->_auth->getIdentity()->role != \Entities\User::TYPE_SUPER_ADMIN) {
            $form->getElement('owner')->setAttrib('disabled', 'disabled');
        }

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($params)) {
                $project = new \Entities\Project;

                $project->setTitle($params['title']);
                $project->setDescription($params['description']);

                $ownerUser = $this->_em->getRepository('Entities\User')
                    ->findOneBy(array('id' => $this->_auth->getIdentity()->user->getId()));

                if ($this->_auth->getIdentity()->role == \Entities\User::TYPE_SUPER_ADMIN &&
                    (int)$params['owner'] > 0) {
                    $ownerUser = $this->_em->getRepository('Entities\User')
                        ->findOneBy(array('id' => $params['owner']));
                }

                $ownerUser->addProject($project);
                $this->_em->persist($ownerUser);

                try {
                    $this->_em->flush();
                    if(strlen(trim($params['users'])) > 0) {
                        $parsedUsers = explode(';', $params['users']);
                        foreach($parsedUsers as $parsedUser) {
                            $parsedValues = explode(',', $parsedUser);

                            // try to find the user and scenario first
                            $user = $this->_em->getRepository('Entities\User')
                                ->findOneBy(array('email' => $parsedValues[0], 'creator' => $ownerUser->getId()));

                            $scenario = $this->_em->getRepository('Entities\Scenario')
                                ->findOneBy(array('id' => $parsedValues[2]));

                            if (is_null($user) || is_null($scenario))
                                continue;

                            $invitation = new \Entities\Invitation;
                            $invitation->setStatus(Entities\Invitation::STATUS_IN_PROGRESS);
                            $invitation->setType($parsedValues[1]);

                            // asssign invitation to parent object
                            $user->addInvitation($invitation);
                            $project->addInvitation($invitation);
                            $scenario->addInvitation($invitation);

                            $this->_em->persist($user);
                            $this->_em->persist($project);
                            $this->_em->persist($scenario);
                        }
                    }

                    $this->_em->flush();

                    // send invitation to the user here

                    // set successful flash message
                    $this->_messages[] = array(
                        'type' => SG_View_Helper_Alert::TYPE_SUCCESS,
                        'text' => $this->_translator->_("A project has been successfully createad and users have been invited.")
                    );

                    $form = new Application_Form_Project($this->_em);

                } catch(Exception $e) {
                    // set error on database flash message
                    $this->_messages[] = array(
                        'type' => SG_View_Helper_Alert::TYPE_ERROR,
                        'text' => $this->_translator->_("Server is busy to process your request. Please try again later.") . $e->getMessage()
                    );
                }
            }
        }

        $form->setLegend($this->_translator->_("Create new project"));
        $form->getElement('submit')->setLabel($this->_translator->_("Create project"));

        $this->view->form = $form;
    }

    public function editAction()
    {
        $params = $this->getRequest()->getParams();
        $queryParams = array('id' => $params['id']);

        if ($this->_auth->getIdentity()->role == Entities\User::TYPE_ADMIN) {
            $queryParams['user'] = $this->_auth->getIdentity()->user->getId();
        }

        $project = $this->_em->getRepository('Entities\Project')
            ->findOneBy($queryParams);

        if (is_null($project)) {
            $this->_redirect($this->_helper->RouteUrl('cloud-project-index'));
        }

        $form = new Application_Form_Project($this->_em, $params);
        $form->getElement('submit')->setLabel($this->_translator->_("Update project"));

        if ($this->_auth->getIdentity()->role != \Entities\User::TYPE_SUPER_ADMIN ||
            (sizeof($project->getInvitations()) > 0 && $project->getUser()->getType() == \Entities\User::TYPE_ADMIN)) {
            $form->getElement('owner')->setAttrib('disabled', 'disabled');
        }

        $usersString = '';
        $usersArray = array();

        foreach($project->getInvitations() as $invitation) {
            $usersArray[] .= $invitation->getUser()->getEmail() . ',' .
                $invitation->getType() . ',' . $invitation->getScenario()->getId();
        }

        $usersString = join(';', $usersArray);

        $form->getElement('title')->setValue($project->getTitle());
        $form->getElement('description')->setValue($project->getDescription());
        $form->getElement('owner')->setValue($project->getUser()->getId());
        $form->getElement('users')->setValue($usersString);

        $form->populate($params);

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($params)) {
                $project->setTitle($params['title']);
                $project->setDescription($params['description']);

                $ownerUser = $this->_em->getRepository('Entities\User')
                    ->findOneBy(array('id' => $project->getUser()->getId()));

                if ($this->_auth->getIdentity()->role == \Entities\User::TYPE_SUPER_ADMIN) {
                    if ($project->getUser()->getId() != $params['owner'] &&
                        (int) $params['owner'] > 0) {
                        $ownerUser->getProjects()->removeElement($project);
                        $this->_em->persist($ownerUser);

                        $ownerUser = $this->_em->getRepository('Entities\User')
                            ->findOneBy(array('id' => $params['owner']));
                        $ownerUser->addProject($project);
                        $this->_em->persist($ownerUser);
                    }
                }

                $this->_em->persist($project);

                try {
                    $this->_em->flush();

                    if (strlen($params['users']) > 0) { // parameter 'users' length > 0

                        if($params['users'] != $usersString) {
                            $parsedUsers = explode(';', $params['users']);
                            $usersNotToUpdate = array();

                            foreach($parsedUsers as $parsedUser) {
                                $foundIndex = array_search($parsedUser, $usersArray);

                                // update to the list where the record should be delete
                                if ($foundIndex !== FALSE & (int)$foundIndex >= 0) {
                                    $usersNotToUpdate[] = $usersArray[$foundIndex];
                                    unset($usersArray[$foundIndex]);
                                }
                            }


                            // delete the existing record in the old users array
                            foreach($usersArray as $parsedUser) {
                                if (strlen($parsedUser) > 0) {
                                    $parsedValues = explode(',', $parsedUser);

                                    $user = $this->_em->getRepository('Entities\User')
                                            ->findOneBy(array('email' => $parsedValues[0], 'creator' => $ownerUser->getId()));

                                    if (!is_null($user)) {
                                        $invitation = $this->_em->getRepository('Entities\Invitation')
                                            ->findOneBy(array('user' => $user->getId(), 'project' => $project->getId()));

                                        $this->_em->remove($invitation);
                                        $this->_em->flush();
                                    }
                                }
                            }

                            // start inserting new record
                            foreach($parsedUsers as $parsedUser) {
                                if(!in_array($parsedUser, $usersNotToUpdate)) {
                                    $parsedValues = explode(',', $parsedUser);

                                    // try to find the user and scenario first
                                    $user = $this->_em->getRepository('Entities\User')
                                        ->findOneBy(array('email' => $parsedValues[0], 'creator' => $ownerUser->getId()));

                                    $scenario = $this->_em->getRepository('Entities\Scenario')
                                        ->findOneBy(array('id' => $parsedValues[2]));

                                    if (is_null($user) || is_null($scenario))
                                        continue;

                                    $invitation = new \Entities\Invitation;

                                    $invitation->setStatus(Entities\Invitation::STATUS_IN_PROGRESS);
                                    $invitation->setType($parsedValues[1]);

                                    $this->_em->persist($invitation);

                                    // asssign invitation to parent object
                                    $user->addInvitation($invitation);
                                    $project->addInvitation($invitation);
                                    $scenario->addInvitation($invitation);

                                    $this->_em->persist($user);
                                    $this->_em->persist($project);
                                    $this->_em->persist($scenario);
                                }
                            }

                            $this->_em->flush();
                        }
                    } else { // parameter 'users' length == 0
                        foreach($usersArray as $parsedUser) {
                            $parsedValues = explode(',', $parsedUser);

                            $user = $this->_em->getRepository('Entities\User')
                                    ->findOneBy(array('email' => $parsedValues[0]));

                            if (!is_null($user)) {
                                $invitation = $this->_em->getRepository('Entities\Invitation')
                                    ->findOneBy(array('user' => $user->getId(), 'project' => $project->getId()));

                                $this->_em->remove($invitation);
                                $this->_em->flush();
                            }
                        }
                    }

                    // set successful flash message
                    $this->_messages[] = array(
                        'type' => SG_View_Helper_Alert::TYPE_SUCCESS,
                        'text' => $this->_translator->_("A project has been successfully updated.")
                    );

                } catch(Exception $e) {
                    // set error on database flash message
                    $this->_messages[] = array(
                        'type' => SG_View_Helper_Alert::TYPE_ERROR,
                        'text' => $this->_translator->_("Server is busy to process your request. Please try again later.") . $e->getMessage()
                    );
                }
            }
        }

        $this->view->form = $form;
    }

    public function deleteAction()
    {
        // set the controller not to render any view
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $params = $this->getRequest()->getParams();

        if (array_key_exists('id', $params) && is_numeric($params['id'])) {
            $project = $this->_em->getRepository('Entities\Project')
                ->findOneBy(array('id' => $params['id']));

            if(!is_null($project)) {
                try {
                    $this->_em->remove($project);
                    $this->_em->flush();

                    $this->_helper->FlashMessenger->addMessage($this->_translator->_("The project has been deleted."));
                } catch (Exception $e) {
                    $this->_helper->FlashMessenger->addMessage($this->_translator->_("Server is busy, please delete the project later."));
                }
            }
        }

        $this->_redirect($this->_helper->RouteUrl('cloud-project-index'));
    }

    public function detailAction()
    {
        $params = $this->getRequest()->getParams();

        $project = $this->_em->getRepository('Entities\Project')
            ->findOneBy(array('id' => $params['id']));

        if (is_null($project)) {
            $this->_redirect($this->_helper->RouteUrl('cloud-project-index'));
        }

        $this->view->project = $project;
        $this->view->em = $this->_em;
    }

    public function chartDetailAction()
    {
        $params = $this->getRequest()->getParams();

        $project = $this->_em->getRepository('Entities\Project')
            ->findOneBy(array('id' => $params['id']));

        if (is_null($project)) {
            $this->_redirect($this->_helper->RouteUrl('cloud-project-index'));
        }

        $this->view->project = $project;
        $this->view->em = $this->_em;

        if ($this->_auth->getIdentity()->role == \Entities\User::TYPE_USER) {
            $this->view->leftMenuIndex = 1;
        }
    }
}