<?php

namespace App\Repository;

use App\Entity\SourceAuth;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SourceAuth>
 *
 * @method SourceAuth|null find($id, $lockMode = null, $lockVersion = null)
 * @method SourceAuth|null findOneBy(array $criteria, array $orderBy = null)
 * @method SourceAuth[]    findAll()
 * @method SourceAuth[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SourceAuthRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SourceAuth::class);
    }

    public function save(SourceAuth $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SourceAuth $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}