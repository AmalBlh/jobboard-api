<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\JobOfferRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: JobOfferRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['job_offer:read']],
    denormalizationContext: ['groups' => ['job_offer:write']],
    operations: [
        new GetCollection(),
        new Get(),
        new Post(security: "is_granted('ROLE_ADMIN')"),
        new Put(security: "is_granted('ROLE_ADMIN')"),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'title'         => 'partial',
    'city'          => 'exact',
    'contractType'  => 'exact',
    'techStack'     => 'partial',
    'company.name'  => 'partial',
])]
#[ApiFilter(BooleanFilter::class, properties: ['isActive'])]
#[ApiFilter(RangeFilter::class, properties: ['salaryMin', 'salaryMax'])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'salaryMin'], arguments: ['orderParameterName' => 'order'])]
class JobOffer
{
    public const CONTRACT_TYPES = ['CDI', 'CDD', 'STAGE', 'ALTERNANCE', 'FREELANCE', 'INTERIM'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['job_offer:read', 'application:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['job_offer:read', 'job_offer:write', 'application:read'])]
    #[Assert\NotBlank]
    private ?string $title = null;

    #[ORM\Column(type: 'text')]
    #[Groups(['job_offer:read', 'job_offer:write'])]
    #[Assert\NotBlank]
    private ?string $description = null;

    #[ORM\Column(length: 50)]
    #[Groups(['job_offer:read', 'job_offer:write', 'application:read'])]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: self::CONTRACT_TYPES)]
    private ?string $contractType = null;

    #[ORM\Column(length: 100)]
    #[Groups(['job_offer:read', 'job_offer:write'])]
    #[Assert\NotBlank]
    private ?string $city = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['job_offer:read', 'job_offer:write'])]
    private ?bool $isRemote = false;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['job_offer:read', 'job_offer:write'])]
    private ?string $techStack = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['job_offer:read', 'job_offer:write'])]
    private ?int $salaryMin = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['job_offer:read', 'job_offer:write'])]
    private ?int $salaryMax = null;

    #[ORM\Column]
    #[Groups(['job_offer:read', 'job_offer:write'])]
    private bool $isActive = true;

    #[ORM\ManyToOne(inversedBy: 'jobOffers')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['job_offer:read', 'job_offer:write'])]
    #[Assert\NotNull]
    private ?Company $company = null;

    #[ORM\OneToMany(mappedBy: 'jobOffer', targetEntity: Application::class)]
    private Collection $applications;

    #[ORM\Column]
    #[Groups(['job_offer:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['job_offer:read', 'job_offer:write'])]
    private ?\DateTimeImmutable $expiresAt = null;

    public function __construct()
    {
        $this->applications = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getTitle(): ?string { return $this->title; }
    public function setTitle(string $title): static { $this->title = $title; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(string $description): static { $this->description = $description; return $this; }
    public function getContractType(): ?string { return $this->contractType; }
    public function setContractType(string $contractType): static { $this->contractType = $contractType; return $this; }
    public function getCity(): ?string { return $this->city; }
    public function setCity(string $city): static { $this->city = $city; return $this; }
    public function getIsRemote(): ?bool { return $this->isRemote; }
    public function setIsRemote(?bool $isRemote): static { $this->isRemote = $isRemote; return $this; }
    public function getTechStack(): ?string { return $this->techStack; }
    public function setTechStack(?string $techStack): static { $this->techStack = $techStack; return $this; }
    public function getSalaryMin(): ?int { return $this->salaryMin; }
    public function setSalaryMin(?int $salaryMin): static { $this->salaryMin = $salaryMin; return $this; }
    public function getSalaryMax(): ?int { return $this->salaryMax; }
    public function setSalaryMax(?int $salaryMax): static { $this->salaryMax = $salaryMax; return $this; }
    public function isIsActive(): bool { return $this->isActive; }
    public function setIsActive(bool $isActive): static { $this->isActive = $isActive; return $this; }
    public function getCompany(): ?Company { return $this->company; }
    public function setCompany(?Company $company): static { $this->company = $company; return $this; }
    public function getApplications(): Collection { return $this->applications; }
    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function getExpiresAt(): ?\DateTimeImmutable { return $this->expiresAt; }
    public function setExpiresAt(?\DateTimeImmutable $expiresAt): static { $this->expiresAt = $expiresAt; return $this; }
}