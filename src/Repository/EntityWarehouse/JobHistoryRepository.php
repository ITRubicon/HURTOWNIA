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
        $job = $this->entityManager->find(JobHistory::class, $id);
        $job->setEnd(new \DateTime());        
        if ($job->getStatus() !== JobStatus::ERROR)
            $job->setStatus(JobStatus::ENDED);
            
        $this->entityManager->persist($job);    
        $this->entityManager->flush();
    }

    public function setError(int $id, string $msg)
    {
        $job = $this->entityManager->find(JobHistory::class, $id);
        $job->setError($msg)
            ->setStatus(JobStatus::ERROR);
        $this->entityManager->persist($job);    
        $this->entityManager->flush();
    }
}
