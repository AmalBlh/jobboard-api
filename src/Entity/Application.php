<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\ApplicationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ApplicationRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['application:read']],
    denormalizationContext: ['groups' => ['application:write']],
    operations: [
        new GetCollection(security: "is_granted('ROLE_USER')"),
        new Get(security: "is_granted('ROLE_ADMIN') or object.getCandidate() == user"),
        new Post(security: "is_granted('ROLE_USER')"),
        new Put(security: "is_granted('ROLE_ADMIN')"),
        new Delete(security: "is_granted('ROLE_ADMIN') or object.getCandidate() == user"),
    ]
)]
class Application
{
    public const STATUSES = ['PENDING', 'REVIEWING', 'INTERVIEW', 'ACCEPTED', 'REJECTED'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['application:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'applications')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['application:read', 'application:write'])]
    #[Assert\NotNull]
    private ?User $candidate = null;

    #[ORM\ManyToOne(inversedBy: 'applications')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['application:read', 'application:write'])]
    #[Assert\NotNull]
    private ?JobOffer $jobOffer = null;

    #[ORM\Column(length: 50)]
    #[Groups(['application:read', 'application:write'])]
    #[Assert\Choice(choices: self::STATUSES)]
    private string $status = 'PENDING';

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['application:read', 'application:write'])]
    private ?string $coverLetter = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['application:read', 'application:write'])]
    private ?string $resumeUrl = null;

    #[ORM\Column]
    #[Groups(['application:read'])]
    private ?\DateTimeImmutable $appliedAt = null;

    public function __construct()
    {
        $this->appliedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getCandidate(): ?User { return $this->candidate; }
    public function setCandidate(?User $candidate): static { $this->candidate = $candidate; return $this; }
    public function getJobOffer(): ?JobOffer { return $this->jobOffer; }
    public function setJobOffer(?JobOffer $jobOffer): static { $this->jobOffer = $jobOffer; return $this; }
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this; }
    public function getCoverLetter(): ?string { return $this->coverLetter; }
    public function setCoverLetter(?string $coverLetter): static { $this->coverLetter = $coverLetter; return $this; }
    public function getResumeUrl(): ?string { return $this->resumeUrl; }
    public function setResumeUrl(?string $resumeUrl): static { $this->resumeUrl = $resumeUrl; return $this; }
    public function getAppliedAt(): ?\DateTimeImmutable { return $this->appliedAt; }
}