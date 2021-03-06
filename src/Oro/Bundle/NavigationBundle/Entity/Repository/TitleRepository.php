<?php

namespace Oro\Bundle\NavigationBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class TitleRepository extends EntityRepository
{
    /**
     * Returns not empty titles array
     *
     * @param array $routes route names to get titles for
     * @return array
     */
    public function getTitles($routes = array())
    {
        $qb = $this
            ->createQueryBuilder('title')
            ->andWhere('title.title <> :title')
            ->setParameter('title', '');

        if (!empty($routes)) {
            $qb->andWhere('title.route IN (:routes)')
               ->setParameter('routes', $routes);
        }

        return $qb->getQuery()->getArrayResult();
    }
}
