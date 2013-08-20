<?php

namespace Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Scenario
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Scenario extends EntityRepository
{
    public function getSelectOptions()
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->add('select', 'a')
           ->add('from', 'Entities\Scenario a')
           ->add('orderBy', 'a.title ASC');

        $options = array();

        foreach($qb->getQuery()->getResult() as $record) {
            $options[$record->getId()] = $record->getTitle();
        }

        return $options;
    }

    public function getElementCategories($scenario)
    {
        $assessmentCategories = new ArrayCollection;

        foreach($scenario->getElements() as $element) {
            if($element->getAssessmentCategory() &&
                !$assessmentCategories->contains($element->getAssessmentCategory()))
                $assessmentCategories[] = $element->getAssessmentCategory();
        }

        return $assessmentCategories;
    }

    public function getMainCategories($scenario)
    {
        $mainCategories = new ArrayCollection;

        foreach($this->getElementCategories($scenario) as $elementCategory) {
            $mainCategory = $this->_em->getRepository('Entities\AssessmentCategory')
                ->getRootCategoryByCategory($elementCategory);

            if ($mainCategory->getType() != \Entities\AssessmentCategory::TYPE_OVERALL &&
                !$mainCategories->contains($mainCategory))
                $mainCategories[] = $mainCategory;
        }

        return $mainCategories;
    }

}