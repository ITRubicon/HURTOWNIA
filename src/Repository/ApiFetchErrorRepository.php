<?php

namespace App\Repository;

use App\Entity\ApiFetchError;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ApiFetchError>
 *
 * @method ApiFetchError|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApiFetchError|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApiFetchError[]    findAll()
 * @method ApiFetchError[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApiFetchErrorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApiFetchError::class);
    }

    public function save(ApiFetchError $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ApiFetchError $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function clearErrors(): int
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "DELETE FROM api_fetch_error WHERE `time` <= '" . date('Y-m-d H:i:s') . "'";
        $stmt = $conn->prepare($sql);
        $res = $stmt->executeQuery();

        return $res->rowCount() ?? 0;
    }

    public function getErrorsCount(): int
    {
        $qb = $this->createQueryBuilder('error');
        $qb->select('COUNT(error.id) as total');

        return (int)$qb->getQuery()->getSingleScalarResult();
    }
}
