<?php

namespace Repositories;

use Doctrine\ORM\EntityRepository;

/**
 * Answer
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Answer extends EntityRepository
{
    public function getAnswersByCategoryAndProject($category, $project, $scenarioIds = array())
    {
        $endNodeCategories = $this->_em->getRepository('Entities\AssessmentCategory')
            ->getEndNodeCategoriesByCategory($category);

        $endNodeCategoryIds = array();

        foreach($endNodeCategories as $endNodeCategory) {
            $endNodeCategoryIds[] = $endNodeCategory->getId();
        }

        $queryString = "SELECT a FROM Entities\Answer a" .
            " WHERE a.element IN (SELECT b.id FROM Entities\Scenario\Element b WHERE b.assessmentCategory IN (" . join(',', $endNodeCategoryIds) . "))" .
            " AND a.invitation IN (SELECT c.id FROM Entities\Invitation c WHERE c.project = ". $project->getId();

        if (sizeof($scenarioIds) == 1) {
            $queryString .= " AND c.scenario = " . $scenarioIds[0];
        } else if (sizeof($scenarioIds) > 1) {
            $queryString .= " AND c.scenario IN (" . join(',', $scenarioIds) . ")";
        }

        $queryString .= ")";

        $query = $this->_em->createQuery($queryString);
        $result = $query->getResult();

        return $result;
    }

    public function getAnswersByElementAndProject($element, $project)
    {
        $queryString = "SELECT a FROM Entities\Answer a" .
            " WHERE a.element = " . $element->getId() .
            " AND a.invitation IN (SELECT c.id FROM Entities\Invitation c WHERE c.project = ". $project->getId() . ")";

        $query = $this->_em->createQuery($queryString);
        $result = $query->getResult();

        return $result;
    }

}