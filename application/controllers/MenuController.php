<?php

/**
 * Controller that handles User menu.
 *
 * This controller handles the user left menu area of the portal
 *
 * @category   Subgroup
 * @package    Controller
 * @author     Agustinus Prasetyo Widodo <agustinus.widodo@gmail.com>
 * @copyright  2013 Agustinus Prasetyo
 * @version    SVN: $Id$
 * @link       https://cloud.subgroup.se
 */

class MenuController extends SG_Controller_Action
{
    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        $total = array();

        if ($this->_auth->getIdentity()->role == Entities\User::TYPE_SUPER_ADMIN) {
            $total['user'] = sizeof($this->_em->getRepository("Entities\User")->findAll());
            $total['project'] = sizeof($this->_em->getRepository("Entities\Project")->findAll());
            $total['scenario'] = sizeof($this->_em->getRepository("Entities\Scenario")->findAll());
            $total['assessment'] = sizeof($this->_em->getRepository("Entities\AssessmentCategory")->findBy(array("type" => NULL)));
        } else if ($this->_auth->getIdentity()->role == Entities\User::TYPE_ADMIN) {
            $total['user'] = sizeof($this->_em->getRepository("Entities\User")->findBy(array('creator' => $this->_auth->getIdentity()->user->getId())));
            $total['project'] = sizeof($this->_em->getRepository("Entities\Project")->findBy(array('user' => $this->_auth->getIdentity()->user->getId())));
        } else {
            $dashboardCounter = 0;
            $resultCounter = 0;

            $user = $this->_em->getRepository('Entities\User')
                ->findOneBy(array('id' => $this->_auth->getIdentity()->user->getId()));

            foreach($user->getInvitations() as $invitation) {
                if ($invitation->getStatus() == Entities\Invitation::STATUS_IN_PROGRESS) {
                    $dashboardCounter += 1;
                }

                $answeredPercentages = array();
                foreach($invitation->getProject()->getInvitations() as $projectInvitation) {
                    $answeredPercentages[] = $this->_em->getRepository('Entities\Invitation')
                        ->getPercentageAnsweredRequiredElementByInvitation($projectInvitation);
                }

                // get percentage of answers from invited user to the project
                if (array_sum($answeredPercentages)/sizeof($answeredPercentages) >= 75) {
                    $resultCounter += 1;
                }
            }

            $total['dashboard'] = $dashboardCounter;
            $total['result'] = $resultCounter;
        }

        $this->view->total = $total;

    }

}