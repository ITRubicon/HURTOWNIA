<?php

namespace App\Repository\EntityWarehouse;

use App\EntityWarehouse\JobHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JobHistory>
 *
 * @method JobHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method JobHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method JobHistory[]    findAll()
 * @method JobHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobHistoryRepository
{
    private $em = null;
    private $qb = null;

    public function __construct(ManagerRegistry $registry)
    {
        $this->em = $registry->getManager('warehouse');
        $this->qb = new QueryBuilder($this->em);
    }

    public function save(JobHistory $entity, bool $flush = false)
    {
        $this->em->persist($entity);

        if ($flush)
            $this->em->flush();

        return $entity->getId();
    }

    public function remove(JobHistory $entity, bool $flush = false): void
    {
        $this->em->remove($entity);

        if ($flush)
            $this->em->flush();
    }

    public function setEnd(int $id)
    {
        $this->qb
            ->update(JobHistory::class, 'j')
            ->set('j.end', ':time')
            ->set('j.status', ':status')
            ->where('j.id = :id')
            ->setParameter('time', new \DateTime())
            ->setParameter('id', $id)
            ->setParameter('status', 1)
            ->getQuery()
            ->execute();
    }

    public function setError(int $id, string $msg)
    {
        $this->qb
            ->update(JobHistory::class, 'j')
            ->set('j.error', ':error')
            ->set('j.status', ':status')
            ->where('j.id = :id')
            ->setParameter('error', $msg)
            ->setParameter('id', $id)
            ->setParameter('status', -1)
            ->getQuery()
            ->execute();
    }

//    /**
//     * @return JobHistory[] Returns an array of JobHistory objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('j.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?JobHistory
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
