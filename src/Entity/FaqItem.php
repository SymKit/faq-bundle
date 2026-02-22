<?php

declare(strict_types=1);

namespace Symkit\FaqBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symkit\FaqBundle\Repository\FaqItemRepository;

#[ORM\Entity(repositoryClass: FaqItemRepository::class)]
#[ORM\HasLifecycleCallbacks]
class FaqItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Faq::class, inversedBy: 'getFaqItems')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull(groups: ['create', 'edit'])]
    private ?Faq $faq = null;

    #[ORM\Column(length: 500)]
    #[Assert\NotBlank(groups: ['create', 'edit'])]
    #[Assert\Length(max: 500, groups: ['create', 'edit'])]
    private ?string $question = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(groups: ['create', 'edit'])]
    private ?string $answer = null;

    #[ORM\Column]
    #[Assert\NotNull(groups: ['create', 'edit'])]
    #[Assert\PositiveOrZero(groups: ['create', 'edit'])]
    private ?int $position = 0;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFaq(): ?Faq
    {
        return $this->faq;
    }

    public function setFaq(?Faq $faq): static
    {
        $this->faq = $faq;

        return $this;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): static
    {
        $this->question = $question;

        return $this;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): static
    {
        $this->answer = $answer;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function __toString(): string
    {
        return (string) $this->getQuestion();
    }
}
