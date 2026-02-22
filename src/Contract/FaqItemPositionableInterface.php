<?php

declare(strict_types=1);

namespace Symkit\FaqBundle\Contract;

/**
 * Entity contract for FAQ items that can be positioned within a FAQ.
 * Used by FaqItemPositioner to reorder positions.
 */
interface FaqItemPositionableInterface
{
    public function getFaq(): ?object;

    public function getPosition(): ?int;

    public function getId(): ?int;

    public function setPosition(int $position): static;
}
