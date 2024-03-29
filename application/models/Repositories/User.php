<?php

namespace Repositories;

use Doctrine\ORM\EntityRepository;

/**
 * User
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class User extends EntityRepository
{
    public function searchEmails($searchKey, $exceptions = array(), $owner = null)
    {
        $queryString = "SELECT a FROM Entities\\User a WHERE a.email LIKE '%$searchKey%' AND a.type = 'U'";

        if (sizeof($exceptions) > 0) {
            $exceptionString = '';
            foreach($exceptions as $index => $exception) {
                $exceptionString .= "'" . $exception . "'";
                if ($index != sizeof($exceptions) - 1)
                    $exceptionString .= ",";
            }

            rtrim($exceptionString, '\,');
            $queryString .= sprintf(" AND a.email NOT IN (%s)", $exceptionString);
        }

        if ($owner) {
            $queryString .= " AND a.creator = " . $owner->getId();
        }

        $query = $this->_em->createQuery($queryString);

        $options = array();

        foreach($query->getResult() as $record) {
            $options[] = $record->getEmail();
        }

        return $options;
    }
}