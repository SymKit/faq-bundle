<?php

declare(strict_types=1);

namespace Symkit\FaqBundle\Tests\Unit\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symkit\CrudBundle\Event\CrudEvent;
use Symkit\FaqBundle\Entity\Faq;
use Symkit\FaqBundle\Entity\FaqItem;
use Symkit\FaqBundle\EventListener\FaqItemPositionListener;
use Symkit\FaqBundle\Manager\FaqItemPositioner;

final class FaqItemPositionListenerTest extends TestCase
{
    public function testOnCrudEventCallsFlushForFaqItem(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('flush');
        $query = $this->createMock(\Doctrine\ORM\Query::class);
        $query->method('getResult')->willReturn([]);
        $qb = $this->createMock(\Doctrine\ORM\QueryBuilder::class);
        $qb->method('select')->willReturnSelf();
        $qb->method('from')->willReturnSelf();
        $qb->method('where')->willReturnSelf();
        $qb->method('setParameter')->willReturnSelf();
        $qb->method('orderBy')->willReturnSelf();
        $qb->method('addOrderBy')->willReturnSelf();
        $qb->method('andWhere')->willReturnSelf();
        $qb->method('getQuery')->willReturn($query);
        $em->method('createQueryBuilder')->willReturn($qb);

        $positioner = new FaqItemPositioner($em, FaqItem::class);
        $listener = new FaqItemPositionListener($positioner, FaqItem::class);

        $faq = new Faq();
        $faq->setTitle('Test');
        $faq->setCode('test');
        $item = new FaqItem();
        $item->setFaq($faq);
        $item->setQuestion('Q');
        $item->setAnswer('A');
        $item->setPosition(0);
        $event = new CrudEvent($item, null, null);
        $listener->onCrudEvent($event);
    }

    public function testOnCrudEventDoesNotCallFlushForOtherEntity(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('flush');
        $em->expects(self::never())->method('createQueryBuilder');

        $positioner = new FaqItemPositioner($em, FaqItem::class);
        $listener = new FaqItemPositionListener($positioner, FaqItem::class);

        $event = new CrudEvent(new \stdClass(), null, null);
        $listener->onCrudEvent($event);
    }
}
