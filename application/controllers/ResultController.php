<?php

/**
 * Controller that handles the result of consultancy.
 *
 * This controller handles the result view of the consulted user
 *
 * @category   Subgroup
 * @package    Controller
 * @author     Agustinus Prasetyo Widodo <agustinus.widodo@gmail.com>
 * @copyright  2013 Agustinus Prasetyo
 * @version    SVN: $Id$
 * @link       https://cloud.subgroup.se
 */

class ResultController extends SG_Controller_Action
{

    public function init()
    {
        parent::init();
        $this->view->leftMenuIndex = 1;
    }

    /**
     * Show the list of the user result if any result occured
     */
    public function indexAction()
    {
        $params = $this->getRequest()->getParams();

        $project = $this->_em->getRepository('Entities\Project')
            ->findOneBy(array('id' => $params['id']));

        if (is_null($project)) {
            $this->_messages = array(
                array(
                    'text' => $this->_translator->_("Project not found."),
                    'type' => SG_View_Helper_Alert::TYPE_INFO,
                )
            );
        }

        if($this->_auth->getIdentity()->role == \Entities\User::TYPE_USER) {
            $this->view->userRoleInProject = $this->_em->getRepository('Entities\Project')
                ->getUserRoleInProject($project, $this->_auth->getIdentity()->user);
        }

        // get percentage of answers from invited user to the project
        $answeredPercentages = array();
        foreach($project->getInvitations() as $invitation) {
            $answeredPercentages[] = $this->_em->getRepository('Entities\Invitation')
                ->getPercentageAnsweredRequiredElementByInvitation($invitation);
        }

        $means = array();
        if (array_sum($answeredPercentages)/sizeof($answeredPercentages) >= 75) {
            // get overall type of assessment categories
            $overallCategories = $this->_em->getRepository('Entities\AssessmentCategory')
                ->getEndNodeCategories(Repositories\AssessmentCategory::END_NODE_MODE_OVERALL_ONLY);

            // get types of user and scenarios being used in the project
            $userTypesAndScenarios = $this->_em->getRepository('Entities\Project')
                ->getTypesAndScenariosUsed($project);

            $typesWithMergedScenarios = array();

            foreach($userTypesAndScenarios as $typeAndScenarios) {
                if(in_array($typeAndScenarios->getType(), $typesWithMergedScenarios)) {
                    $typesWithMergedScenarios[$typeAndScenarios->getType()][] = $typeAndScenarios->getScenario()->getId();
                } else {
                    $typesWithMergedScenarios[$typeAndScenarios->getType()] = array($typeAndScenarios->getScenario()->getId());
                }
            }

            // foreach category get the answer mean value
            foreach($overallCategories as $overallCategory) {
                if ((bool)$overallCategory->getVisible() === true) {
                    foreach($typesWithMergedScenarios as $type => $mergedScenarios) {
                        $answers = $this->_em->getRepository('Entities\Answer')
                            ->getAnswersByCategoryAndProject($overallCategory, $project, $mergedScenarios);
                        $means[$overallCategory->getName()][$type] = sprintf("%.2f", $this->_helper->MeanFromAnswers($answers));
                    }

                }
            }
            $this->view->means = $means;

        } else {
            $this->_messages = array(
                array(
                    'text' => $this->_translator->_("Result can not be calculated yet until 75 percent of the group have submitted."),
                    'type' => SG_View_Helper_Alert::TYPE_INFO,
                )
            );
        }

        $this->view->project = $project;
    }

    public function detailAction()
    {
        $params = $this->getRequest()->getParams();

        $project = $this->_em->getRepository('Entities\Project')
            ->findOneBy(array('id' => $params['id']));

        if (is_null($project)) {
            $this->_messages = array(
                array(
                    'text' => $this->_translator->_("Project not found."),
                    'type' => SG_View_Helper_Alert::TYPE_INFO,
                )
            );
        }

        // get percentage of answers from invited user to the project
        $answeredPercentages = array();
        foreach($project->getInvitations() as $invitation) {
            $answeredPercentages[] = $this->_em->getRepository('Entities\Invitation')
                ->getPercentageAnsweredRequiredElementByInvitation($invitation);
        }

        $means = array();
        if (array_sum($answeredPercentages)/sizeof($answeredPercentages) >= 75) {
            // get overall type of assessment categories
            $mainCategories = $this->_em->getRepository('Entities\Project')
                ->getMainCategories($project);

            $means = array();

            // get types of user and scenarios being used in the project
            $userTypesAndScenarios = $this->_em->getRepository('Entities\Project')
                ->getTypesAndScenariosUsed($project);

            $typesWithMergedScenarios = array();
            $scenariosUsed = array();

            foreach($userTypesAndScenarios as $typeAndScenarios) {
                if(in_array($typeAndScenarios->getType(), $typesWithMergedScenarios)) {
                    $typesWithMergedScenarios[$typeAndScenarios->getType()][] = $typeAndScenarios->getScenario()->getId();
                } else {
                    $typesWithMergedScenarios[$typeAndScenarios->getType()] = array($typeAndScenarios->getScenario()->getId());
                }
                $scenariosUsed[] = $typeAndScenarios->getScenario();
            }

            // foreach category get the answer mean value
            foreach($mainCategories as $mainCategory) {
                $means[] = $this->recursiveMeans($mainCategory, $project, $typesWithMergedScenarios);
            }

            $this->view->means = $means;
            $this->view->scenariosUsed = $scenariosUsed;

        } else {
            $this->_messages = array(
                array(
                    'text' => $this->_translator->_("Result can not be calculated yet until 75 percent of the group have submitted."),
                    'type' => SG_View_Helper_Alert::TYPE_INFO,
                )
            );
        }

        if ($this->_auth->getIdentity()->role != Entities\User::TYPE_USER)
            $this->view->leftMenuIndex = 4;
        $this->view->project = $project;
    }

    protected function recursiveMeans($category, $project, $typesWithMergedScenarios)
    {
        $meanInfo = array(
            'id' => $category->getId(),
            'name' => $category->getName(),
            'mean' => array(),
            'stats' => array(),
            'childs' => array()
        );

        foreach($typesWithMergedScenarios as $type => $mergedScenarios) {
            $answers = $this->_em->getRepository('Entities\Answer')
                ->getAnswersByCategoryAndProject($category, $project, $mergedScenarios);

            $meanInfo['mean'][$type] = sprintf("%.2f", $this->_helper->MeanFromAnswers($answers));
            $meanInfo['stats'][$type] = array(
                "0" => 0, "1" => 0, "2" => 0, "2.5" => 0, "3" => 0, "4" => 0, "5" => 0,
            );

            foreach($answers as $answer) {
                $decodedJson = json_decode($answer->getElement()->getJsonDefaultValue(),true);
                $maxJsonDefaultValue = 0;
                foreach($decodedJson['values'] as $key => $value) {
                    if ($key > $maxJsonDefaultValue)
                        $maxJsonDefaultValue = $key;
                }
                if($maxJsonDefaultValue != 5) {
                    $answerValue = ((int)$answer->getValue() != 0) ? ((int)$answer->getValue()/$maxJsonDefaultValue)*5 : 0;
                } else {
                    $answerValue = (int)$answer->getValue();
                }
                $meanInfo['stats'][$type][$answerValue] += 1;
            }
        }

        if (sizeof($category->getChildCategories()) > 0) {
            $childMeans = array();
            foreach($category->getChildCategories() as $childCategory) {
                $childMeanInfo = $this->recursiveMeans($childCategory, $project, $typesWithMergedScenarios);
                $meanInfo['childs'][] = $childMeanInfo;

                foreach($childMeanInfo['mean'] as $type => $childMean) {
                    $childMeans[$type][] = $childMean;
                }
            }

            foreach($childMeans as $type => $childMean) {
                $meanInfo['mean'][$type] = sprintf("%.2f",array_sum($childMean)/sizeof($childMean));
            }
        }

        return $meanInfo;
    }

}

