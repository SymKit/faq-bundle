<?php

declare(strict_types=1);

namespace Symkit\FaqBundle\Tests\Unit\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Symkit\FaqBundle\Entity\Faq;
use Symkit\FaqBundle\Entity\FaqItem;
use Symkit\FaqBundle\Manager\FaqItemPositioner;

final class FaqItemPositionerTest extends TestCase
{
    public function testReorderPositionsEarlyReturnWhenNotFaqItemInstance(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('createQueryBuilder');
        $em->expects(self::never())->method('flush');

        $positioner = new FaqItemPositioner($em, FaqItem::class);
        $positioner->reorderPositions(new \stdClass());
    }

    public function testReorderPositionsEarlyReturnWhenFaqIsNull(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('createQueryBuilder');
        $em->expects(self::never())->method('flush');

        $item = new FaqItem();
        $item->setPosition(0);
        self::assertNull($item->getFaq());

        $positioner = new FaqItemPositioner($em, FaqItem::class);
        $positioner->reorderPositions($item);
    }

    public function testReorderPositionsCallsFlushWhenFaqSet(): void
    {
        $faq = new Faq();
        $faq->setTitle('Test');
        $faq->setCode('test');
        $item = new FaqItem();
        $item->setFaq($faq);
        $item->setQuestion('Q');
        $item->setAnswer('A');
        $item->setPosition(0);

        $query = $this->createMock(Query::class);
        $query->method('getResult')->willReturn([]);

        $qb = $this->createMock(QueryBuilder::class);
        $qb->method('select')->willReturnSelf();
        $qb->method('from')->willReturnSelf();
        $qb->method('where')->willReturnSelf();
        $qb->method('setParameter')->willReturnSelf();
        $qb->method('orderBy')->willReturnSelf();
        $qb->method('addOrderBy')->willReturnSelf();
        $qb->method('andWhere')->willReturnSelf();
        $qb->method('getQuery')->willReturn($query);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('createQueryBuilder')->willReturn($qb);
        $em->expects(self::once())->method('flush');

        $positioner = new FaqItemPositioner($em, FaqItem::class);
        $positioner->reorderPositions($item);

        self::assertSame(0, $item->getPosition());
    }
}
