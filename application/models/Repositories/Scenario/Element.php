<?php

namespace Repositories\Scenario;

use Doctrine\ORM\EntityRepository;

/**
 * Element
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Element extends EntityRepository
{
    public function getRequiredElementsByScenario($scenario)
    {
        return $this->_em->getRepository('Entities\Scenario\Element')->findBy(array('isRequired' => true, 'scenario' => $scenario->getId()));
    }
}