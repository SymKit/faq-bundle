<?php

declare(strict_types=1);

namespace Symkit\FaqBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symkit\FaqBundle\Contract\FaqByCodeResolverInterface;
use Twig\Environment;

final readonly class FaqController
{
    public function __construct(
        private FaqByCodeResolverInterface $faqByCodeResolver,
        private Environment $twig,
    ) {
    }

    public function show(string $code): Response
    {
        $faq = $this->faqByCodeResolver->findByCode($code);

        if (!$faq) {
            return new Response($this->twig->render('@SymkitFaq/faq/components/_not_found.html.twig', [
                'code' => $code,
            ]));
        }

        return new Response($this->twig->render('@SymkitFaq/faq/components/_faq.html.twig', [
            'faq' => $faq,
        ]));
    }
}
