<?php

declare(strict_types=1);

namespace Symkit\FaqBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<object>
 */
class FaqItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, string $entityClass)
    {
        parent::__construct($registry, $entityClass);
    }

    /**
     * @return list<object>
     */
    public function findByFaq(object $faq): array
    {
        /** @var list<object> $result */
        $result = $this->createQueryBuilder('f')
            ->where('f.faq = :faq')
            ->setParameter('faq', $faq)
            ->orderBy('f.position', 'ASC')
            ->getQuery()
            ->getResult();

        return $result;
    }
}
