<?php

declare(strict_types=1);

namespace Symkit\FaqBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symkit\CrudBundle\Controller\AbstractCrudController;
use Symkit\FaqBundle\Contract\FaqByIdResolverInterface;
use Symkit\FaqBundle\Entity\Faq;
use Symkit\FaqBundle\Entity\FaqItem;

final class FaqItemController extends AbstractCrudController
{
    public function __construct(
        private readonly FaqByIdResolverInterface $faqByIdResolver,
        private readonly string $faqItemClass,
        private readonly string $faqItemFormClass,
        private readonly TranslatorInterface $translator,
    ) {
    }

    protected function getEntityClass(): string
    {
        return $this->faqItemClass;
    }

    protected function getFormClass(): string
    {
        return $this->faqItemFormClass;
    }

    protected function getRoutePrefix(): string
    {
        return 'admin_faq_item';
    }

    public function create(int $faqId, Request $request): Response
    {
        $faq = $this->faqByIdResolver->findFaqById($faqId);
        if (!$faq instanceof Faq) {
            throw $this->createNotFoundException($this->translator->trans('admin.faq.not_found', [], 'SymkitFaqBundle'));
        }

        $item = new $this->faqItemClass();
        if (!$item instanceof FaqItem) {
            throw new \InvalidArgumentException(\sprintf('Configured faq_item_class must extend %s.', FaqItem::class));
        }
        $item->setFaq($faq);
        $item->setPosition($faq->getFaqItems()->count());

        return $this->renderNew($item, $request, [
            'page_title' => $this->translator->trans('admin.faq_item.create.title', [], 'SymkitFaqBundle'),
            'page_description' => $this->translator->trans('admin.faq_item.create.description', ['%title%' => $faq->getTitle()], 'SymkitFaqBundle'),
            'template_vars' => [
                'back_route' => 'admin_faq_edit',
                'back_route_params' => ['id' => $faq->getId()],
            ],
            'redirect_route' => 'admin_faq_edit',
            'redirect_params' => ['id' => $faq->getId()],
        ]);
    }

    public function edit(FaqItem $item, Request $request): Response
    {
        $questionSummary = mb_substr($item->getQuestion() ?? '', 0, 40).'...';

        return $this->renderEdit($item, $request, [
            'page_title' => $this->translator->trans('admin.faq_item.edit.title', ['%question%' => $questionSummary], 'SymkitFaqBundle'),
            'page_description' => $this->translator->trans('admin.faq_item.edit.description', [], 'SymkitFaqBundle'),
            'template_vars' => [
                'back_route' => 'admin_faq_edit',
                'back_route_params' => ['id' => $this->getFaqIdFromItem($item)],
            ],
            'redirect_route' => 'admin_faq_edit',
            'redirect_params' => ['id' => $this->getFaqIdFromItem($item)],
        ]);
    }

    private function getFaqIdFromItem(FaqItem $item): int
    {
        $faq = $item->getFaq();
        if (null === $faq) {
            throw new \LogicException('FaqItem must have an associated Faq.');
        }
        $id = $faq->getId();
        if (null === $id) {
            throw new \LogicException('Faq must be persisted.');
        }

        return $id;
    }

    public function delete(FaqItem $item, Request $request): Response
    {
        $faq = $item->getFaq();
        if (null === $faq) {
            throw new \LogicException('FaqItem must have an associated Faq.');
        }
        $faqId = $faq->getId();
        $this->performDelete($item, $request);

        return $this->redirectToRoute('admin_faq_edit', ['id' => $faqId]);
    }
}
