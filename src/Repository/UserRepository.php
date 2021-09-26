<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param string $email
     * @return int|mixed|string|null
     * @throws NonUniqueResultException
     */
    public function loadUserByUserName(string $email)
    {
        $qb = $this->createQueryBuilder('u');
        $qb->andWhere($qb->expr()->eq('u.email', ':email'))
            ->setParameter('email', $email);
        return $qb->getQuery()->getOneOrNullResult();
    }
}