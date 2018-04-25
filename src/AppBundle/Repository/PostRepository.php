<?php

namespace AppBundle\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator;
use AppBundle\Entity\Post;

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

    public function getPosts($page=1, $maxperpage=10)
    {
        $q = $this->createQueryBuilder('p')
            ->andWhere('p.status IN (:status)')
            ->setParameter('status', array(Post::PUBLISHED, Post::FEATURED))
            ->orderBy('p.status', 'DESC')
            ->addOrderBy('p.publishedAt', 'DESC');

        $q->setFirstResult(($page-1) * $maxperpage)->setMaxResults($maxperpage);

        return new Paginator($q);
    }

}
