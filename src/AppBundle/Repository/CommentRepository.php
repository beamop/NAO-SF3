<?php

namespace AppBundle\Repository;

/**
 * CommentRepository
 */
class CommentRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Get comments on hold
     *
     * @return array
     */
    public function findAllNoValidatedComments()
    {
        return $this->createQueryBuilder('o')
            ->where('o.status = 0')
            ->getQuery()
            ->getResult();
    }
}
