<?php

namespace App\Repository\EntityWarehouse;

use App\EntityWarehouse\JobHistory;
use Doctrine\Persistence\ManagerRegistry;

class JobHistoryRepository
{
    private $entityManager = null;

    public function __construct(ManagerRegistry $registry)
    {
        $this->entityManager = $registry->getManager('warehouse');
    }

    public function save(JobHistory $entity, bool $flush = false)
    {
        $this->entityManager->persist($entity);

        if ($flush)
            $this->entityManager->flush();

        return $entity->getId();
    }

    public function remove(JobHistory $entity, bool $flush = false): void
    {
        $this->entityManager->remove($entity);

        if ($flush)
            $this->entityManager->flush();
    }

    public function setEnd(int $id)
    {
        $dql = "UPDATE App\EntityWarehouse\JobHistory j SET j.end = :end, j.status = :status WHERE j.id = :id";
        $this->entityManager->createQuery($dql)
            ->setParameter('id', $id)
            ->setParameter('end', new \DateTime())
            ->setParameter('status', 1)
            ->execute();
    }

    public function setError(int $id, string $msg)
    {
        $dql = "UPDATE App\EntityWarehouse\JobHistory j SET j.error = :error, j.end = :end, j.status = :status WHERE j.id = :id";
        $this->entityManager->createQuery($dql)
            ->setParameter('id', $id)
            ->setParameter('error', $msg)
            ->setParameter('status', -1)
            ->execute();
    }
}
