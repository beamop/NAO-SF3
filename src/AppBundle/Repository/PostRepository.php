<?php

namespace AppBundle\Repository;

/**
 * PostRepository
 */
class PostRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * Get last posts by date of publication and/or featured
     *
     * @param $name
     * @return array
     */

    /*
    public function lastPosts($name)
    {
        return $this->createQueryBuilder('p')
            ->select('p')
            //->where('b.nomCourant LIKE :name')->setParameter('name', '%'.$name.'%')
            ->andWhere('b.nomCourant LIKE :name', 'b.id = b.ref')
            ->setParameter('name', '%'.$name.'%')
            ->addOrderBy('p.publishedAt', 'DESC');
            ->getQuery()
            ->getResult();
    }
    */

}
