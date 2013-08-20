<?php

/**
 * Controller that handles the form operation.
 *
 * This controller handles the form operation by the invited user
 *
 * @category   Subgroup
 * @package    Controller
 * @author     Agustinus Prasetyo Widodo <agustinus.widodo@gmail.com>
 * @copyright  2013 Agustinus Prasetyo
 * @version    SVN: $Id$
 * @link       https://cloud.subgroup.se
 */

class FormController extends SG_Controller_Action
{

    public function init()
    {
        parent::init();
    }

    public function editAction()
    {
        $params = $this->getRequest()->getParams();

        $invitation = $this->_em->getRepository('Entities\Invitation')
            ->findOneBy(array('id' => $params['id']));

        if(is_null($invitation)) {
            if($this->_auth->getIdentity()->role == Entities\User::TYPE_USER) {
                $this->_redirect($this->_helper->RouteUrl('cloud-dashboard'));
            } else {
                $this->_redirect($this->_helper->RouteUrl('cloud-user-index'));
            }
        }

        $this->view->leftMenuIndex = 0;

        if($this->_auth->getIdentity()->role == Entities\User::TYPE_ADMIN) {
            $this->view->leftMenuIndex = 3;
        }

        if($this->getRequest()->isPost()) {
            foreach($params as $key => $value) {
                $found = strpos($key, 'element_form_');
                if($found !== FALSE && is_int($found)) {
                    $elementId = substr($key, strlen('element_form_'));

                    $element = $this->_em->getRepository('Entities\Scenario\Element')
                        ->findOneBy(array('id' => $elementId));

                    if(!is_null($element)) {
                        // try to find the answer if exist
                        $answer = $this->_em->getRepository('Entities\Answer')
                            ->findOneBy(array('element' => $element->getId(), 'invitation' => $invitation->getId()));

                        if(is_null($answer)) {
                            $answer = new Entities\Answer;

                            $invitation->addAnswer($answer);
                            $element->addAnswer($answer);

                            $this->_em->persist($invitation);
                            $this->_em->persist($element);
                        }

                        $answer->setValue($value);

                        $this->_em->persist($answer);
                    }
                }
            }

            try{
                $this->_em->flush();
                $this->_messages[] = array(
                    'type' => SG_View_Helper_Alert::TYPE_SUCCESS,
                    'text' => $this->_translator->_("Your answers has been successfully saved.")
                );
            } catch(Exception $e) {
                $this->_messages[] = array(
                    'type' => SG_View_Helper_Alert::TYPE_,
                    'text' => $this->_translator->_("Server is busy to process your request. Please try again later.")
                );
            }
        }

        // check for progress percentage
        $progress = $this->_em->getRepository('Entities\Invitation')
            ->getPercentageAnsweredRequiredElementByInvitation($invitation);

        if ($progress == 100) {
            $invitation->setStatus(Entities\Invitation::STATUS_FINISHED);
            $this->_em->persist($invitation);
            try{
                $this->_em->flush();
                $this->_messages[] = array(
                    'type' => SG_View_Helper_Alert::TYPE_SUCCESS,
                    'text' => $this->_translator->_("Congratulations! You have finished your assessment.")
                );
            } catch(Exception $e) {
                $this->_messages[] = array(
                    'type' => SG_View_Helper_Alert::TYPE_,
                    'text' => $this->_translator->_("Server is busy to process your request. Please try again later.")
                );
            }
        }

        $this->view->progress = $progress;
        $this->view ->disableForm = true;

        // check if the form owned by the authenticated user
        if ($this->_auth->getIdentity()->user->getId() == $invitation->getUser()->getId() &&
            $progress < 100) {
            $this->view->disableForm = false;
        }

        $formAnswers = array();
        foreach($invitation->getAnswers() as $answer) {
            $formAnswers['element_form_' . $answer->getElement()->getId()]= $answer->getValue();
        }

        $this->view->invitation = $invitation;
        $this->view->formAnswers = $formAnswers;
    }

}

