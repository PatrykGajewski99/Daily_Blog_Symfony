<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getByEmail(string $email): ?User
    {
       $entityManager = $this->getEntityManager();

       $query = $entityManager->createQuery(
           'SELECT user FROM App\Entity\User user WHERE user.email = :email'
       )->setParameter("email", $email);

       return $query->getOneOrNullResult();
    }

    public function getById(string $userId): ?User
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT user FROM App\Entity\User user WHERE user.id = :id'
        )->setParameter("id", $userId);

        return $query->getOneOrNullResult();
    }
}
