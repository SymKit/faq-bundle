<?php

declare(strict_types=1);

namespace Symkit\FaqBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symkit\CrudBundle\Controller\AbstractCrudController;
use Symkit\FaqBundle\Entity\Faq;

final class FaqController extends AbstractCrudController
{
    public function __construct(
        private readonly string $faqClass,
        private readonly string $faqFormClass,
        private readonly TranslatorInterface $translator,
    ) {
    }

    protected function getEntityClass(): string
    {
        return $this->faqClass;
    }

    protected function getFormClass(): string
    {
        return $this->faqFormClass;
    }

    protected function getRoutePrefix(): string
    {
        return 'admin_faq';
    }

    protected function configureListFields(): array
    {
        return [
            'title' => [
                'label' => $this->translator->trans('list.title', [], 'SymkitFaqBundle'),
                'sortable' => true,
            ],
            'code' => [
                'label' => $this->translator->trans('list.code', [], 'SymkitFaqBundle'),
                'sortable' => true,
                'cell_class' => 'font-mono text-xs',
            ],
            'getFaqItems' => [
                'label' => $this->translator->trans('list.items', [], 'SymkitFaqBundle'),
                'template' => '@SymkitCrud/crud/field/count.html.twig',
                'icon' => 'heroicons:chat-bubble-left-right-20-solid',
            ],
            'actions' => [
                'label' => '',
                'template' => '@SymkitCrud/crud/field/actions.html.twig',
                'edit_route' => 'admin_faq_edit',
                'header_class' => 'text-right',
                'cell_class' => 'text-right',
            ],
        ];
    }

    protected function configureSearchFields(): array
    {
        return ['title', 'code'];
    }

    public function list(Request $request): Response
    {
        return $this->renderIndex($request, [
            'page_title' => $this->translator->trans('admin.faq.list.title', [], 'SymkitFaqBundle'),
            'page_description' => $this->translator->trans('admin.faq.list.description', [], 'SymkitFaqBundle'),
        ]);
    }

    public function create(Request $request): Response
    {
        $entity = new $this->faqClass();
        if (!$entity instanceof Faq) {
            throw new \InvalidArgumentException(\sprintf('Configured faq_class must extend %s.', Faq::class));
        }

        return $this->renderNew($entity, $request, [
            'page_title' => $this->translator->trans('admin.faq.create.title', [], 'SymkitFaqBundle'),
            'page_description' => $this->translator->trans('admin.faq.create.description', [], 'SymkitFaqBundle'),
        ]);
    }

    public function edit(Faq $faq, Request $request): Response
    {
        return $this->renderEdit($faq, $request, [
            'page_title' => $faq->getTitle(),
            'page_description' => $this->translator->trans('admin.faq.edit.description', [], 'SymkitFaqBundle'),
            'after_form_template' => '@SymkitFaq/faq/admin/_items_list.html.twig',
            'extra_nav_items_template' => '@SymkitFaq/faq/admin/_items_nav_link.html.twig',
            'template_vars' => [
                'faq' => $faq,
            ],
        ]);
    }

    public function delete(Faq $faq, Request $request): Response
    {
        return $this->performDelete($faq, $request);
    }
}
