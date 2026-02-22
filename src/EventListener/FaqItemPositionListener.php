<?php

declare(strict_types=1);

namespace Symkit\FaqBundle\EventListener;

use Symkit\CrudBundle\Event\CrudEvent;
use Symkit\FaqBundle\Manager\FaqItemPositioner;

final readonly class FaqItemPositionListener
{
    public function __construct(
        private FaqItemPositioner $faqItemPositioner,
        private string $faqItemClass,
    ) {
    }

    public function onCrudEvent(CrudEvent $event): void
    {
        $entity = $event->getEntity();

        if (!$entity instanceof $this->faqItemClass) {
            return;
        }

        $this->faqItemPositioner->reorderPositions($entity);
    }
}
