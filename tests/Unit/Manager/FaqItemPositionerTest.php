<?php

declare(strict_types=1);

namespace Symkit\FaqBundle\Tests\Unit\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Symkit\FaqBundle\Contract\FaqItemPositionableInterface;
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

        $stub = new class implements FaqItemPositionableInterface {
            public function getFaq(): ?object
            {
                return null;
            }

            public function getPosition(): int
            {
                return 0;
            }

            public function getId(): ?int
            {
                return null;
            }

            public function setPosition(int $position): static
            {
                return $this;
            }
        };

        $positioner = new FaqItemPositioner($em, FaqItem::class);
        $positioner->reorderPositions($stub);
    }

    public function testReorderPositionsEarlyReturnWhenInterfaceButNotFaqItemClass(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('createQueryBuilder');
        $em->expects(self::never())->method('flush');

        $stub = new class implements FaqItemPositionableInterface {
            public function getFaq(): object
            {
                return new \stdClass();
            }

            public function getPosition(): int
            {
                return 0;
            }

            public function getId(): ?int
            {
                return null;
            }

            public function setPosition(int $position): static
            {
                return $this;
            }
        };

        $positioner = new FaqItemPositioner($em, FaqItem::class);
        $positioner->reorderPositions($stub);
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

    public function testReorderPositionsReordersExistingItemsAndCurrentItem(): void
    {
        $faq = new Faq();
        $faq->setTitle('Test');
        $faq->setCode('test');
        $item0 = new FaqItem();
        $item0->setFaq($faq);
        $item0->setQuestion('Q0');
        $item0->setAnswer('A0');
        $item0->setPosition(0);
        $item1 = new FaqItem();
        $item1->setFaq($faq);
        $item1->setQuestion('Q1');
        $item1->setAnswer('A1');
        $item1->setPosition(1);
        $currentItem = new FaqItem();
        $currentItem->setFaq($faq);
        $currentItem->setQuestion('Q2');
        $currentItem->setAnswer('A2');
        $currentItem->setPosition(2);

        $query = $this->createMock(Query::class);
        $query->method('getResult')->willReturn([$item0, $item1]);

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
        $positioner->reorderPositions($currentItem);

        self::assertSame(0, $item0->getPosition());
        self::assertSame(1, $item1->getPosition());
        self::assertSame(2, $currentItem->getPosition());
    }

    public function testReorderPositionsInsertsCurrentAtNewPosition(): void
    {
        $faq = new Faq();
        $faq->setTitle('Test');
        $faq->setCode('test');
        $existing = new FaqItem();
        $existing->setFaq($faq);
        $existing->setQuestion('Q');
        $existing->setAnswer('A');
        $existing->setPosition(0);
        $currentItem = new FaqItem();
        $currentItem->setFaq($faq);
        $currentItem->setQuestion('Q2');
        $currentItem->setAnswer('A2');
        $currentItem->setPosition(1);

        $query = $this->createMock(Query::class);
        $query->method('getResult')->willReturn([$existing]);

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
        $positioner->reorderPositions($currentItem);

        self::assertSame(0, $existing->getPosition());
        self::assertSame(1, $currentItem->getPosition());
    }

    public function testReorderPositionsExcludesCurrentItemByIdWhenIdIsNotNull(): void
    {
        $faq = new Faq();
        $faq->setTitle('Test');
        $faq->setCode('test');
        $currentItem = new FaqItem();
        $currentItem->setFaq($faq);
        $currentItem->setQuestion('Q');
        $currentItem->setAnswer('A');
        $currentItem->setPosition(0);
        $reflection = new \ReflectionClass(FaqItem::class);
        $idProp = $reflection->getProperty('id');
        $idProp->setValue($currentItem, 99);

        $otherItem = new FaqItem();
        $otherItem->setFaq($faq);
        $otherItem->setQuestion('Q2');
        $otherItem->setAnswer('A2');
        $otherItem->setPosition(1);

        $query = $this->createMock(Query::class);
        $query->method('getResult')->willReturn([$otherItem]);

        $qb = $this->createMock(QueryBuilder::class);
        $setParameterCalls = [];
        $qb->method('select')->willReturnSelf();
        $qb->method('from')->willReturnSelf();
        $qb->method('where')->willReturnSelf();
        $qb->method('setParameter')->willReturnCallback(function (string $key, $value) use ($qb, &$setParameterCalls) {
            $setParameterCalls[$key] = $value;

            return $qb;
        });
        $qb->method('orderBy')->willReturnSelf();
        $qb->method('addOrderBy')->willReturnSelf();
        $qb->method('andWhere')->willReturnSelf();
        $qb->method('getQuery')->willReturn($query);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('createQueryBuilder')->willReturn($qb);
        $em->expects(self::once())->method('flush');

        $positioner = new FaqItemPositioner($em, FaqItem::class);
        $positioner->reorderPositions($currentItem);

        self::assertArrayHasKey('currentId', $setParameterCalls);
        self::assertSame(99, $setParameterCalls['currentId']);
        self::assertSame(0, $currentItem->getPosition());
        self::assertSame(1, $otherItem->getPosition());
    }
}
