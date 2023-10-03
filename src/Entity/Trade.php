<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use App\Controller\UserTradeController;
use App\Repository\TradeRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\PrePersist;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TradeRepository::class)]
#[ApiResource(
    operations: [
        new \ApiPlatform\Metadata\Post(
            openapiContext: [
                'summary' => 'Créer un nouveau trade',
                'description' => 'Permet de créer un trade',
            ],
        ),
        new Delete(),
        new Patch(
            uriTemplate: '/trade/{id}',
            openapiContext: [
                'summary' => 'Mettre à jour le trade',
                'description' => 'Permet de mettre à jour le statut, ajouter la room au trade, ou encore de mettre à jour si le user à fermer son trade',
            ],
            normalizationContext: ["groups" => ["tradePatch:read"]],
            denormalizationContext: ["groups" => ["tradePatch:write"]],
        ),
        new Get(),
        new GetCollection(
            uriTemplate: '/users/{id}/trade',
            controller: UserTradeController::class,
            openapiContext: [
                'summary' => 'Lister les trades d\'un utilisateur',
                'description' => 'Permet d\'obtenir une liste des trades d\'un utilisateur spécifique.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'in' => 'path',
                        'required' => true,
                        'description' => 'ID de l\'utilisateur',
                        'schema' => [
                            'type' => 'integer'
                        ]
                    ]
                ],
            ],
            paginationEnabled: false,
            filters: ["order"],
        ),
    ],
    normalizationContext: ["groups" => ["trade:read"]],
    denormalizationContext: ["groups" => ["trade:write"]],
)]
#[ORM\HasLifecycleCallbacks]
class Trade
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[Groups(["trade:read", "trade:write", "tradePatch:read", 'read:Room', 'write:Room', 'read:Review', 'write:Review'])]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'trades')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["trade:read", "trade:write", "tradePatch:read", 'read:Room', 'write:Room', 'read:Review', 'write:Review', 'write:User', 'read:User'])]
    private ?User $applicant = null;

    #[ORM\ManyToOne(inversedBy: 'trades')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["trade:read", "trade:write", "tradePatch:read", 'read:Room', 'write:Room', 'read:Review', 'write:Review', 'write:User', 'read:User'])]
    private ?User $userPostOwner = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["trade:read", "trade:write", "tradePatch:read", 'read:Room', 'write:Room', 'read:Review', 'write:Review'])]
    private ?Post $post = null;

    #[ORM\Column(length: 255)]
    #[Groups(["trade:read", "trade:write", "tradePatch:write", "tradePatch:read", 'read:Room', 'write:Room', 'read:Review', 'write:Review'])]
    private ?string $statut = null;

    #[ORM\OneToOne(mappedBy: 'trade', targetEntity: Room::class, cascade: ['persist', 'remove'])]
    #[Groups(["trade:read", "trade:write", "tradePatch:write", "tradePatch:read", 'read:Room', 'write:Room'])]
    private ?Room $room = null;

    #[ORM\Column]
    #[Groups(["trade:read", "trade:write", "tradePatch:read", 'read:Room', 'write:Room', 'read:Review', 'write:Review'])]
    #[ApiProperty(readable: true, writable: false)]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(["trade:read", "trade:write", "tradePatch:write", "tradePatch:read", 'read:Room', 'write:Room'])]
    private bool $applicantDeleted = false;

    #[ORM\Column(nullable: true)]
    #[Groups(["trade:read", "trade:write", "tradePatch:write", "tradePatch:read", 'read:Room', 'write:Room'])]
    private bool $postOwnerDeleted = false;

    #[ORM\OneToMany(mappedBy: 'Trade', targetEntity: UserReview::class)]
    private Collection $userReviews;

    public function __construct()
    {
        $this->userReviews = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getApplicant(): ?User
    {
        return $this->applicant;
    }

    public function setApplicant(?User $applicant): static
    {
        $this->applicant = $applicant;

        return $this;
    }

    public function getUserPostOwner(): ?User
    {
        return $this->userPostOwner;
    }

    public function setUserPostOwner(?User $userPostOwner): static
    {
        $this->userPostOwner = $userPostOwner;

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): static
    {
        $this->post = $post;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): static
    {
        // unset the owning side of the relation if necessary
        if ($room === null && $this->room !== null) {
            $this->room->setTrade(null);
        }

        // set the owning side of the relation if necessary
        if ($room !== null && $room->getTrade() !== $this) {
            $room->setTrade($this);
        }

        $this->room = $room;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    #[PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new DateTimeImmutable();
    }

    public function isApplicantDeleted(): ?bool
    {
        return $this->applicantDeleted;
    }

    public function setApplicantDeleted(?bool $applicantDeleted): static
    {
        $this->applicantDeleted = $applicantDeleted;

        return $this;
    }

    public function isPostOwnerDeleted(): ?bool
    {
        return $this->postOwnerDeleted;
    }

    public function setPostOwnerDeleted(?bool $postOwnerDeleted): static
    {
        $this->postOwnerDeleted = $postOwnerDeleted;

        return $this;
    }

    /**
     * @return Collection<int, UserReview>
     */
    public function getUserReviews(): Collection
    {
        return $this->userReviews;
    }

    public function addUserReview(UserReview $userReview): static
    {
        if (!$this->userReviews->contains($userReview)) {
            $this->userReviews->add($userReview);
            $userReview->setTrade($this);
        }

        return $this;
    }

    public function removeUserReview(UserReview $userReview): static
    {
        if ($this->userReviews->removeElement($userReview)) {
            // set the owning side to null (unless already changed)
            if ($userReview->getTrade() === $this) {
                $userReview->setTrade(null);
            }
        }

        return $this;
    }

}
