<?php

/**
 * Controller that handles the assessment category of consultancy.
 *
 * This controller handles the assessment category for the scenario
 *
 * @category   Subgroup
 * @package    Controller
 * @author     Agustinus Prasetyo Widodo <agustinus.widodo@gmail.com>
 * @copyright  2013 Agustinus Prasetyo
 * @version    SVN: $Id$
 * @link       https://cloud.subgroup.se
 */

class AssessmentController extends SG_Controller_Action
{

    public function init()
    {
        parent::init();
        $this->view->leftMenuIndex = 6;
    }

    /**
     * Show the list of the user result if any result occured
     */
    public function indexAction()
    {
        $params = $this->getRequest()->getParams();
        $assessmentCategoryPaginator = array();
        $pageNumber = $this->getRequest()->getParam('page', 1);
        $pageSize = $this->getRequest()->getParam('size', 20);

        $form = new Application_Form_AssessmentCategory($this->_em, Application_Form_AssessmentCategory::TYPE_MAIN_CATEGORY);
        $form->setLegend($this->_translator->_("Create new main category"));

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($params)) {
                $category = new \Entities\AssessmentCategory;

                $category->setName($params['name']);
                $category->setDescription($params['description']);
                $category->setType($params['type']);

                $this->_em->persist($category);

                try {
                    $this->_em->flush();

                    // set successful flash message
                    $this->_messages[] = array(
                        'type' => SG_View_Helper_Alert::TYPE_SUCCESS,
                        'text' => $this->_translator->_("A category has been successfully created.")
                    );

                    $form = new Application_Form_AssessmentCategory($this->_em, Application_Form_AssessmentCategory::TYPE_MAIN_CATEGORY);
                }catch(Exception $e) {
                    $this->_messages[] = array(
                        'type' => SG_View_Helper_Alert::TYPE_ERROR,
                        'text' => $this->_translator->_("Server is busy to process your request. Please try again later.")
                    );
                }
            }
        }

        $assessmentCategories = $this->_em->getRepository('Entities\AssessmentCategory')->getMainCategories();

        if (sizeof($assessmentCategories) > 0) {
            $assessmentCategoryPaginator = Zend_Paginator::factory($assessmentCategories);
            $assessmentCategoryPaginator->setCurrentPageNumber($pageNumber)
                ->setItemCountPerPage($pageSize);
        } else {
            $this->_messages = array(
                array(
                    'text' => $this->_translator->_("You have not create any assessment category yet."),
                    'type' => SG_View_Helper_Alert::TYPE_INFO,
                )
            );
        }

        $this->view->assessmentCategoryPaginator = $assessmentCategoryPaginator;
        $this->view->form = $form;
    }

    public function createAction()
    {
        $params = $this->getRequest()->getParams();
        $form = new Application_Form_AssessmentCategory($this->_em, Application_Form_AssessmentCategory::TYPE_SUB_CATEGORY);

        $form->setLegend($this->_translator->_("Create new category"));

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($params)) {
                $category = new \Entities\AssessmentCategory;

                $category->setName($params['name']);
                $category->setDescription($params['description']);
                $category->setVisible(true);

                $parentCategory = $this->_em->getRepository('Entities\AssessmentCategory')
                    ->findOneBy(array('id' => $params['parentCategory']));

                if ($parentCategory) {
                    $parentCategory->addChildCategory($category);
                    $category->setSequence(sizeof($parentCategory->getChildCategories()) + 1);
                }

                $this->_em->persist($category);
                $this->_em->persist($parentCategory);

                try {
                    $this->_em->flush();

                    // set successful flash message
                    $this->_messages[] = array(
                        'type' => SG_View_Helper_Alert::TYPE_SUCCESS,
                        'text' => $this->_translator->_("A category has been successfully created.")
                    );

                    $form = new Application_Form_AssessmentCategory($this->_em, Application_Form_AssessmentCategory::TYPE_SUB_CATEGORY);

                }catch(Exception $e) {
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

        // load a particular category
        $category = $this->_em->getRepository('Entities\AssessmentCategory')
            ->findOneBy(array('id' => $params['id']));

        if (is_null($category)) {
            $this->_redirect($this->_helper->RouteUrl('cloud-assessment-index'));
        }

        $form = new Application_Form_AssessmentCategory($this->_em, Application_Form_AssessmentCategory::TYPE_SUB_CATEGORY);
        $form->getElement('name')->setValue($category->getName());
        $form->getElement('description')->setValue($category->getDescription());
        $form->getElement('parentCategory')
            ->setMultiOptions($this->_em->getRepository('Entities\AssessmentCategory')->getSelectOptions($category->getId()))
            ->setValue($category->getParentCategory()->getId());

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($params)) {
                $category->setName($params['name']);
                $category->setDescription($params['description']);

                if ($params['parentCategory'] != $category->getParentCategory()->getId()) {
                    $parentCategory = $this->_em->getRepository('Entities\AssessmentCategory')
                        ->findOneBy(array('id' => $params['parentCategory']));

                    $category->getParentCategory()->getChildCategories()->removeElement($category);

                    $parentCategory->addChildCategory($category);
                    $this->_em->persist($parentCategory);
                }

                $this->_em->persist($category);

                try {
                    $this->_em->flush();

                    // set successful flash message
                    $this->_messages[] = array(
                        'type' => SG_View_Helper_Alert::TYPE_SUCCESS,
                        'text' => $this->_translator->_("The category has been successfully updated.")
                    );

                }catch(Exception $e) {
                    $this->_messages[] = array(
                        'type' => SG_View_Helper_Alert::TYPE_ERROR,
                        'text' => $this->_translator->_("Server is busy to process your request. Please try again later.")
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
            $category = $this->_em->getRepository('Entities\AssessmentCategory')
                ->findOneBy(array('id' => $params['id']));

            if(!is_null($category)) {
                try {
                    $this->_em->remove($category);
                    $this->_em->flush();

                    $this->_helper->FlashMessenger->addMessage($this->_translator->_("The category has been deleted."));
                } catch (Exception $e) {
                    $this->_helper->FlashMessenger->addMessage($this->_translator->_("Server is busy, please delete the category later."));
                }
            }
        }

        $this->_redirect($this->_helper->RouteUrl('cloud-assessment-index'));
    }

}

