<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class GameRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param User $user
     * @return mixed
     */
    public function findByStatusAndNotMe(User $user){
        $qb = $this->createQueryBuilder('g')
            ->where(':user NOT MEMBER OF g.users')
            ->andWhere('g.isWaiting = 1')
            ->addOrderBy('g.id', 'ASC')
            ->setParameter('user', $user);

        return $qb->getQuery()->getResult();

    }
}
