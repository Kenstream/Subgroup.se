<?php

/**
 * Controller that handles the scenario management.
 *
 * This controller handles the scenario management area of the portal
 *
 * @category   Subgroup
 * @package    Controller
 * @author     Agustinus Prasetyo Widodo <agustinus.widodo@gmail.com>
 * @copyright  2013 Agustinus Prasetyo
 * @version    SVN: $Id$
 * @link       https://cloud.subgroup.se
 *
 */

class ScenarioController extends SG_Controller_Action
{

    public function init()
    {
        parent::init();
        $this->view->leftMenuIndex = 5;
    }

    public function indexAction()
    {
        $scenarioPaginator = array();
        $pageNumber = $this->getRequest()->getParam('page', 1);
        $pageSize = $this->getRequest()->getParam('size', 20);

        $scenarios = $this->_em->getRepository('Entities\Scenario')->findAll();

        if (sizeof($scenarios) > 0) {
            $scenarioPaginator = Zend_Paginator::factory($scenarios);
            $scenarioPaginator->setCurrentPageNumber($pageNumber)
                ->setItemCountPerPage($pageSize);
        } else {
            $this->_messages = array(
                array(
                    'text' => $this->_translator->_("You have not create any scenario yet."),
                    'type' => SG_View_Helper_Alert::TYPE_INFO,
                )
            );
        }

        $this->view->scenarioPaginator = $scenarioPaginator;
        $this->view->scenarios = $scenarios;
    }

    public function createAction()
    {
        $params = $this->getRequest()->getParams();

        $form = new Application_Form_Scenario($this->_em);

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($params)) {
                $scenario = new \Entities\Scenario;

                $scenario->setTitle($params['title']);
                $scenario->setDescription($params['description']);

                // check if scenario should copy from previous scenario
                if ((int)$params['copyFrom'] > 0) {
                    $scenarioToCopy = $this->_em->getRepository('Entities\Scenario')
                        ->findOneBy(array('id' => $params['copyFrom']));

                    foreach($scenarioToCopy->getSections() as $sectionToCopy) {
                        $section = new \Entities\Scenario\Section;

                        $section->setTitle($sectionToCopy->getTitle());
                        $section->setDescription($sectionToCopy->getDescription());
                        $section->setSequence($sectionToCopy->getSequence());

                        $scenario->addSection($section);

                        foreach($sectionToCopy->getElements() as $elementToCopy) {
                            $element = new \Entities\Scenario\Element;

                            $element->setLabel($elementToCopy->getLabel());
                            $element->setInfoText($elementToCopy->getInfoText());
                            $element->setSequence($elementToCopy->getSequence());
                            $element->setType($elementToCopy->getType());
                            $element->setRequired($elementToCopy->isRequired());
                            $element->setJsonDefaultValue($elementToCopy->getJsonDefaultValue());

                            if ((int) $elementToCopy->getAssessmentCategory() > 0) {
                                $assessmentCategory = $this->_em->getRepository('Entities\AssessmentCategory')
                                    ->findOneBy(array('id' => $elementToCopy->getAssessmentCategory()));

                                if (!is_null($assessmentCategory)) {
                                    $assessmentCategory->addElement($element);
                                    $this->_em->persist($assessmentCategory);
                                }
                            }

                            $section->addElement($element);
                            $scenario->addElement($element);

                            $this->_em->persist($section);
                        }

                        $this->_em->persist($scenario);
                    }
                }

                // get current user entity
                $user = $this->_em->getRepository('Entities\User')->findOneBy(array('id' => $this->_auth->getIdentity()->user->getId()));

                $user->addScenario($scenario);

                $this->_em->persist($user);

                try {
                    $this->_em->flush();
                    $this->_helper->FlashMessenger->addMessage($this->_translator->_("A scenario has been created, you can add sections and questions to the scenario."));

                    $this->_redirect($this->_helper->RouteUrl('cloud-scenario-detail', array('id' => $scenario->getId())));
                } catch (Exception $e) {
                    $this->_messages[] = array(
                        'type' => SG_View_Helper_Alert::TYPE_ERROR,
                        'text' => $this->_translator->_("Server is busy to process your request. Please try again later.") . $e->getMessage()
                    );
                }
            }
        }

        $this->view->form = $form;
    }

    public function editAction()
    {
        $params = $this->getRequest()->getParams();

        $scenario = $this->_em->getRepository('Entities\Scenario')
            ->findOneBy(array('id' => $params['id']));

        if (is_null($scenario)) {
            $this->_redirect($this->_helper->RouteUrl('cloud-scenario-index'));
        }

        $form = new Application_Form_Scenario($this->_em);

        $form->getElement('title')->setValue($scenario->getTitle());
        $form->getElement('description')->setValue($scenario->getDescription());
        $form->getElement('submit')->setLabel($this->_translator->_("Save"));
        $form->removeElement('copyFrom');

        $form->setLegend($this->_translator->_("Update scenario information"));

        $form->populate($params);

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($params)) {
                $scenario->setTitle($params['title']);
                $scenario->setDescription($params['description']);

                $this->_em->persist($scenario);

                try {
                    $this->_em->flush();
                    $this->_messages[] = array(
                        'type' => SG_View_Helper_Alert::TYPE_SUCCESS,
                        'text' => $this->_translator->_("The scenario has been successfully updated.")
                    );
                } catch (Exception $e) {
                    $this->_messages[] = array(
                        'type' => SG_View_Helper_Alert::TYPE_ERROR,
                        'text' => $this->_translator->_("Server is busy to process your request. Please try again later.") . $e->getMessage()
                    );
                }
            }
        }

        $this->view->form = $form;
    }

    public function detailAction()
    {
        $params = $this->getRequest()->getParams();

        $scenario = $this->_em->getRepository('Entities\Scenario')
            ->findOneBy(array('id' => $params['id']));

        if (is_null($scenario)) {
            $this->_redirect($this->_helper->RouteUrl('cloud-scenario-index'));
        }

        $this->view->scenario = $scenario;
    }

    public function deleteAction()
    {
        // set the controller not to render any view
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $params = $this->getRequest()->getParams();

        if (array_key_exists('id', $params) && is_numeric($params['id'])) {
            $scenario = $this->_em->getRepository('Entities\Scenario')
                ->findOneBy(array('id' => $params['id']));

            if(!is_null($scenario)) {
                try {
                    $this->_em->remove($scenario);
                    $this->_em->flush();

                    $this->_helper->FlashMessenger->addMessage($this->_translator->_("The scenario has been deleted."));
                } catch (Exception $e) {
                    $this->_helper->FlashMessenger->addMessage($this->_translator->_("Server is busy, please delete the scenario later."));
                }
            }
        }

        $this->_redirect($this->_helper->RouteUrl('cloud-scenario-index'));
    }

    public function questionCreateAction()
    {
        $params = $this->getRequest()->getParams();

        $scenario = $this->_em->getRepository('Entities\Scenario')
            ->findOneBy(array('id' => $params['id']));

        if (is_null($scenario)) {
            $this->_redirect($this->_helper->RouteUrl('cloud-scenario-index'));
        }

        $form = new Application_Form_ScenarioElement($this->_em, $scenario->getId());

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($params)) {
                $element = new \Entities\Scenario\Element;

                $element->setLabel($params['label']);
                $element->setInfoText($params['infoText']);
                $element->setSequence(sizeof($scenario->getElements()));

                // set type
                if (in_array($params['type'], array('text', 'textarea', 'checkbox'))) {
                    $element->setType($params['type']);
                    $element->setRequired(false);
                } else if ($params['type'] == 'select') {
                    $element->setType($params['type']);
                    $element->setRequired(true);
                    $element->setJsonDefaultValue('{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5"}}');
                } else {
                    $element->setType(Entities\Scenario\Element::TYPE_RADIO);
                    $element->setRequired(true);
                    // set assessment category
                    if ((int)$params['category'] > 0) {
                        $assessmentCategory = $this->_em->getRepository('Entities\AssessmentCategory')
                            ->findOneBy(array('id' => $params['category']));

                        if (!is_null($assessmentCategory)) {
                            $assessmentCategory->addElement($element);
                            $this->_em->persist($assessmentCategory);
                        }
                    }

                    switch($params['type']) {
                        case Application_Form_ScenarioElement::TYPE_RADIO_2:
                            $element->setJsonDefaultValue('{"values":{"1":"Nej","2":"Ja"}}');
                            break;
                        case Application_Form_ScenarioElement::TYPE_RADIO_5:
                            $element->setJsonDefaultValue('{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5"}}');
                            break;
                        case Application_Form_ScenarioElement::TYPE_RADIO_5_INVERSE:
                            $element->setJsonDefaultValue('{"values":{"5":"1","4":"2","3":"3","2":"4","1":"5"}}');
                            break;
                        case Application_Form_ScenarioElement::TYPE_RADIO_6_LEADER:
                            $element->setJsonDefaultValue('{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5","0":"Ej tillämpbart"}}');
                            break;
                        case Application_Form_ScenarioElement::TYPE_RADIO_6_MEMBER:
                            $element->setJsonDefaultValue('{"values":{"1":"1","2":"2","3":"3","4":"4","5":"5","0":"Vet ej"}}');
                            break;
                    }
                }

                $section = $this->_em->getRepository('Entities\Scenario\Section')
                    ->findOneBy(array('id' => $params['section']));

                if ($section) {
                    if (sizeof($section->getElements()) > 0) {
                        $element->setSequence($section->getElements()->last()->getSequence());
                    }
                    $section->addElement($element);
                    $this->_em->persist($section);
                }

                $scenario->addElement($element);
                $this->_em->persist($scenario);

                try {
                    $this->_em->flush();
                    $this->_helper->FlashMessenger->addMessage($this->_translator->_("A new question has been created."));

                    $this->_redirect($this->_helper->RouteUrl('cloud-scenario-detail', array('id' => $scenario->getId())) . '#scenarioTabQuestion');
                } catch (Exception $e) {
                    $this->_messages[] = array(
                        'type' => SG_View_Helper_Alert::TYPE_ERROR,
                        'text' => $this->_translator->_("Server is busy to process your request. Please try again later.") . $e->getMessage()
                    );
                }
            }
        }

        $this->view->form = $form;
    }

    public function questionEditAction()
    {
        $params = $this->getRequest()->getParams();

        $element = $this->_em->getRepository('Entities\Scenario\Element')
            ->findOneBy(array('id' => $params['id']));

        if (is_null($element)) {
            $this->_redirect($this->_helper->RouteUrl('cloud-scenario-index'));
        }

        $form = new Application_Form_ScenarioElement($this->_em, $element->getScenario()->getId());

        $form->setLegend($this->_translator->_("Update element information"));
        $form->getElement('submit')->setLabel($this->_translator->_("Save"));
        $form->getElement('label')->setValue($element->getLabel());
        $form->getElement('infoText')->setValue($element->getInfoText());
        $form->getElement('type')->setAttrib('disabled', 'disabled');
        //$form->getElement('section')->setAttrib('disabled', 'disabled');
        //$form->getElement('category')->setAttrib('disabled', 'disabled');

        if ($element->getAssessmentCategory())
            $form->getElement('category')->setValue($element->getAssessmentCategory()->getId());

        if($element->getSection())
            $form->getElement('section')->setValue($element->getSection()->getId());

        if (in_array($element->getType(), array('text', 'textarea', 'checkbox', 'select'))) {
            $form->getElement('type')->setValue($element->getType());
        } else {
            $decodedJsonDefaultValue = json_decode($element->getJsonDefaultValue(), true);
            if(sizeof($decodedJsonDefaultValue['values']) == 5) {
                foreach($decodedJsonDefaultValue['values'] as $key => $value) {
                    if((int)$key == 1) {
                        $form->getElement('type')->setValue(Application_Form_ScenarioElement::TYPE_RADIO_5);
                    } else {
                        $form->getElement('type')->setValue(Application_Form_ScenarioElement::TYPE_RADIO_5_INVERSE);
                    }
                    break;
                }
            } else if(sizeof($decodedJsonDefaultValue['values']) == 6) {
                foreach($decodedJsonDefaultValue['values'] as $key => $value) {
                    if((int)$key == 0 && $value == "Vet ej") {
                        $form->getElement('type')->setValue(Application_Form_ScenarioElement::TYPE_RADIO_6_MEMBER);
                    } else {
                        $form->getElement('type')->setValue(Application_Form_ScenarioElement::TYPE_RADIO_6_LEADER);
                    }
                }
            } else {
                $form->getElement('type')->setValue(Application_Form_ScenarioElement::TYPE_RADIO_2);
            }
        }

        $form->populate($params);

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($params)) {

                $element->setLabel($params['label']);
                $element->setInfoText($params['infoText']);

                if ((int)$params['section'] > 0) {
                    if(is_null($element->getSection()) ||
                        (int)$params['section'] != $element->getSection()->getId()) {
                        $section = $this->_em->getRepository('Entities\Scenario\Section')
                            ->findOneBy(array('id' => $params['section']));

                        if ($section) {
                            $element->setSequence($section->getElements()->last()->getSequence());
                            $section->addElement($element);
                            $this->_em->persist($section);
                        }
                    }
                }

                if ((int)$params['category'] > 0) {
                    if(is_null($element->getAssessmentCategory()) ||
                        (int)$params['category'] != $element->getAssessmentCategory()->getId()) {
                        $assessmentCategory = $this->_em->getRepository('Entities\AssessmentCategory')
                            ->findOneBy(array('id' => $params['category']));

                        if (!is_null($assessmentCategory)) {
                            $assessmentCategory->addElement($element);
                            $this->_em->persist($assessmentCategory);
                        }
                    }
                }

                $this->_em->persist($element);

                try {
                    $this->_em->flush();
                    $this->_helper->FlashMessenger->addMessage($this->_translator->_("The question has been updated."));
                    $this->_redirect($this->_helper->RouteUrl('cloud-scenario-question-edit', array('id' => $element->getId())));
                } catch (Exception $e) {
                    $this->_messages[] = array(
                        'type' => SG_View_Helper_Alert::TYPE_ERROR,
                        'text' => $this->_translator->_("Server is busy to process your request. Please try again later.") . $e->getMessage()
                    );
                }
            }
        }

        $this->view->form = $form;
    }

    public function questionDeleteAction()
    {
        // set the controller not to render any view
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $params = $this->getRequest()->getParams();

        if (array_key_exists('id', $params) && is_numeric($params['id'])) {
            $element = $this->_em->getRepository('Entities\Scenario\Element')
                ->findOneBy(array('id' => $params['id']));

            if(!is_null($element)) {
               $scenarioId = $element->getScenario()->getId();
                try {
                    $this->_em->remove($element);
                    $this->_em->flush();

                    $this->_helper->FlashMessenger->addMessage($this->_translator->_("The question has been deleted."));
                    $this->_redirect($this->_helper->RouteUrl('cloud-scenario-detail', array('id' => $scenarioId)) . '#scenarioTabQuestion');
                } catch (Exception $e) {
                    $this->_helper->FlashMessenger->addMessage($this->_translator->_("Server is busy, please delete the scenario later."));
                }
            }
        }
        $this->_redirect($this->_helper->RouteUrl('cloud-scenario-index'));
    }

    public function sectionCreateAction()
    {
        $params = $this->getRequest()->getParams();

        $scenario = $this->_em->getRepository('Entities\Scenario')
            ->findOneBy(array('id' => $params['id']));

        if (is_null($scenario)) {
            $this->_redirect($this->_helper->RouteUrl('cloud-scenario-index'));
        }

        $form = new Application_Form_ScenarioSection($this->_em);

        $form->getElement('submit')->setLabel($this->_translator->_("Add new"));
        $form->getElement('sequence')->setValue(sizeof($scenario->getSections()) + 1);
        $form->setLegend($this->_translator->_("Add new section"));

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($params)) {
                $section = new \Entities\Scenario\Section;

                $section->setTitle($params['title']);
                $section->setDescription($params['description']);
                $section->setSequence($params['sequence']);

                $scenario->addSection($section);

                $this->_em->persist($section);

                try {
                    $this->_em->flush();
                    $this->_helper->FlashMessenger->addMessage($this->_translator->_("A new section has been created."));

                    $this->_redirect($this->_helper->RouteUrl('cloud-scenario-detail', array('id' => $scenario->getId())));
                } catch (Exception $e) {
                    $this->_messages[] = array(
                        'type' => SG_View_Helper_Alert::TYPE_ERROR,
                        'text' => $this->_translator->_("Server is busy to process your request. Please try again later.") . $e->getMessage()
                    );
                }
            }
        }

        $this->view->form = $form;
    }

    public function sectionEditAction()
    {
        $params = $this->getRequest()->getParams();

        $section = $this->_em->getRepository('Entities\Scenario\Section')
            ->findOneBy(array('id' => $params['id']));

        if (is_null($section)) {
            $this->_redirect($this->_helper->RouteUrl('cloud-scenario-index'));
        }

        $form = new Application_Form_ScenarioSection($this->_em);

        $form->getElement('title')->setValue($section->getTitle());
        $form->getElement('description')->setValue($section->getDescription());
        $form->getElement('sequence')->setValue($section->getSequence());

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($params)) {
                $section->setTitle($params['title']);
                $section->setDescription($params['description']);
                $section->setSequence($params['sequence']);

                $this->_em->persist($section);

                try {
                    $this->_em->flush();

                    $this->_messages[] = array(
                        'type' => SG_View_Helper_Alert::TYPE_SUCCESS,
                        'text' => $this->_translator->_("The section information has been successfully updated.")
                    );
                } catch (Exception $e) {
                    $this->_messages[] = array(
                        'type' => SG_View_Helper_Alert::TYPE_ERROR,
                        'text' => $this->_translator->_("Server is busy to process your request. Please try again later.") . $e->getMessage()
                    );
                }
            }
        }

        $this->view->form = $form;
    }

    public function sectionDeleteAction()
    {
        // set the controller not to render any view
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $params = $this->getRequest()->getParams();

        if (array_key_exists('id', $params) && is_numeric($params['id'])) {
            $section = $this->_em->getRepository('Entities\Scenario\Section')
                ->findOneBy(array('id' => $params['id']));


            if(!is_null($section)) {
                $scenarioId = $section->getScenario()->getId();
                try {
                    $this->_em->remove($section);
                    $this->_em->flush();

                    $this->_helper->FlashMessenger->addMessage($this->_translator->_("The section has been deleted."));
                } catch (Exception $e) {
                    $this->_helper->FlashMessenger->addMessage($this->_translator->_("Server is busy, please delete the scenario later."));
                }
            }
        }

        $this->_redirect($this->_helper->RouteUrl('cloud-scenario-detail', array('id' => $scenarioId)));
    }
}

