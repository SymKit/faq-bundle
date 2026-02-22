<?php

declare(strict_types=1);

namespace Symkit\FaqBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symkit\FaqBundle\Repository\FaqRepository;

final class FaqController extends AbstractController
{
    public function __construct(
        private readonly FaqRepository $faqRepository,
    ) {
    }

    public function show(string $code): Response
    {
        $faq = $this->faqRepository->findByCode($code);

        if (!$faq) {
            return $this->render('@SymkitFaq/faq/components/_not_found.html.twig', [
                'code' => $code,
            ]);
        }

        return $this->render('@SymkitFaq/faq/components/_faq.html.twig', [
            'faq' => $faq,
        ]);
    }
}
