<?php

declare(strict_types=1);

namespace Symkit\FaqBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symkit\FaqBundle\Contract\FaqByCodeResolverInterface;
use Symkit\FaqBundle\Contract\FaqByIdResolverInterface;

/**
 * @extends ServiceEntityRepository<object>
 */
final class FaqRepository extends ServiceEntityRepository implements FaqByCodeResolverInterface, FaqByIdResolverInterface
{
    public function __construct(ManagerRegistry $registry, string $entityClass)
    {
        parent::__construct($registry, $entityClass);
    }

    public function findByCode(string $code): ?object
    {
        return $this->findOneBy(['code' => $code]);
    }

    public function findFaqById(int $id): ?object
    {
        return $this->find($id);
    }
}
