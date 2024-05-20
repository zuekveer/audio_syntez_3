<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UsersRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Users::class);
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $entityManager = $this->getEntityManager();

        if (ctype_xdigit(str_replace('-', '', $identifier)) && 36 === strlen($identifier)) {
            $query = $entityManager->createQuery(
                'SELECT u
            FROM App\Entity\Users u
            WHERE u.guid = :query'
            )->setParameter('query', $identifier);
        } else {
            $query = $entityManager->createQuery(
                'SELECT u
            FROM App\Entity\Users u
            WHERE u.email = :query'
            )->setParameter('query', $identifier);
        }

        return $query->getOneOrNullResult();
    }
}
