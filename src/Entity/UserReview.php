<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Controller\UserReviewsController;
use App\Repository\UserReviewRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Controller\BlockMultipleReviewOnATrade;

#[ORM\Entity(repositoryClass: UserReviewRepository::class)]
#[ApiResource(
    operations: [
        new \ApiPlatform\Metadata\Post(
            uriTemplate: '/reviews',
            controller: BlockMultipleReviewOnATrade::class
        ),
        new GetCollection(
            uriTemplate: '/reviews/{id}/user',
            controller: UserReviewsController::class,
            openapiContext: [
                'summary' => 'Lister les reviews d\'un utilisateur',
                'description' => 'Permet d\'obtenir une liste des review d\'un utilisateur spécifique.',
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
            filters: ["order"]
        )
    ],
    normalizationContext: ["groups" => ["read:Review"]],
    denormalizationContext: ["groups" => ["write:Review"]],
)]
class UserReview
{
    #[ORM\Id]
    #[Groups(['read:Review', 'write:Review', 'read:Post'])]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userReviews')]
    #[Groups(['read:Review', 'write:Review'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'userReviews')]
    #[Groups(['read:Review', 'write:Review'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $targetedUser = null;

    #[ORM\Column]
    #[Groups(['read:Review', 'write:Review'])]
    private ?string $note = null;

    #[ORM\Column]
    #[Groups(['read:Review', 'write:Review', 'read:Post'])]
    private ?int $stars = null;

    #[ORM\ManyToOne(inversedBy: 'userReviews')]
    #[Groups(['read:Review', 'write:Review'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Trade $trade = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getTargetedUser(): ?User
    {
        return $this->targetedUser;
    }

    public function setTargetedUser(?User $targetedUser): static
    {
        $this->targetedUser = $targetedUser;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getStars(): ?int
    {
        return $this->stars;
    }

    public function setStars(int $stars): static
    {
        $this->stars = $stars;

        return $this;
    }

    public function getTrade(): ?Trade
    {
        return $this->trade;
    }

    public function setTrade(?Trade $trade): static
    {
        $this->trade = $trade;

        return $this;
    }

    
}
