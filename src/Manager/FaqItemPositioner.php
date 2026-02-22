<?php

declare(strict_types=1);

namespace Symkit\FaqBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;

final readonly class FaqItemPositioner
{
    /**
     * @param class-string $faqItemClass
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private string $faqItemClass,
    ) {
    }

    public function reorderPositions(object $faqItem): void
    {
        if (!$faqItem instanceof $this->faqItemClass) {
            return;
        }

        // Narrowed type for PHPStan: $faqItem is instance of $this->faqItemClass (entity with getFaq, getPosition, getId, setPosition)
        $faq = $faqItem->getFaq();
        if (!$faq) {
            return;
        }

        $newPosition = $faqItem->getPosition();

        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('f')
            ->from($this->faqItemClass, 'f')
            ->where('f.faq = :faq')
            ->setParameter('faq', $faq)
            ->orderBy('f.position', 'ASC')
            ->addOrderBy('f.id', 'ASC')
        ;

        if (null !== $faqItem->getId()) {
            $qb->andWhere('f.id != :currentId')
                ->setParameter('currentId', $faqItem->getId())
            ;
        }

        /** @var list<object> $existingItems */
        $existingItems = $qb->getQuery()->getResult();

        $orderedItems = [];
        $inserted = false;

        foreach ($existingItems as $item) {
            if (!$inserted && \count($orderedItems) === $newPosition) {
                $orderedItems[] = $faqItem;
                $inserted = true;
            }
            $orderedItems[] = $item;
        }

        if (!$inserted) {
            $orderedItems[] = $faqItem;
        }

        foreach ($orderedItems as $index => $item) {
            /** @var object $item */
            if (method_exists($item, 'setPosition')) {
                $item->setPosition($index);
            }
        }

        $this->entityManager->flush();
    }
}
