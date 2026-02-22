<?php

declare(strict_types=1);

namespace Symkit\FaqBundle\Contract;

/**
 * Resolves a FAQ entity by its id (admin operations).
 */
interface FaqByIdResolverInterface
{
    public function findFaqById(int $id): ?object;
}
