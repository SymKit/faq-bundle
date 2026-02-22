<?php

declare(strict_types=1);

namespace Symkit\FaqBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symkit\FaqBundle\Repository\FaqRepository;

#[ORM\Entity(repositoryClass: FaqRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Faq
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    /** @phpstan-ignore-next-line property.unusedType (Doctrine assigns id on persist) */
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(groups: ['create', 'edit'])]
    #[Assert\Length(max: 255, groups: ['create', 'edit'])]
    private ?string $title = null;

    #[ORM\Column(length: 100, unique: true)]
    #[Assert\NotBlank(groups: ['create', 'edit'])]
    #[Assert\Length(max: 100, groups: ['create', 'edit'])]
    #[Assert\Regex('/^[a-z0-9-]+$/', message: 'validation.faq.code.regex', groups: ['create', 'edit'])]
    private ?string $code = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, FaqItem>
     */
    #[ORM\OneToMany(targetEntity: FaqItem::class, mappedBy: 'faq', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $faqItems;

    public function __construct()
    {
        $this->faqItems = new ArrayCollection();
    }

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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

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

    /**
     * @return Collection<int, FaqItem>
     */
    public function getFaqItems(): Collection
    {
        return $this->faqItems;
    }

    public function addFaqItem(FaqItem $faqItem): static
    {
        if (!$this->faqItems->contains($faqItem)) {
            $this->faqItems->add($faqItem);
            $faqItem->setFaq($this);
        }

        return $this;
    }

    public function removeFaqItem(FaqItem $faqItem): static
    {
        if ($this->faqItems->removeElement($faqItem)) {
            if ($faqItem->getFaq() === $this) {
                $faqItem->setFaq(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->getTitle();
    }
}
