<?php

declare(strict_types=1);

namespace Symkit\FaqBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<object>
 */
class FaqRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, string $entityClass)
    {
        parent::__construct($registry, $entityClass);
    }

    public function findByCode(string $code): ?object
    {
        return $this->findOneBy(['code' => $code]);
    }
}
