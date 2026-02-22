<?php

declare(strict_types=1);

namespace Symkit\FaqBundle\Contract;

/**
 * Resolves a FAQ entity by its unique code (public display).
 */
interface FaqByCodeResolverInterface
{
    public function findByCode(string $code): ?object;
}
