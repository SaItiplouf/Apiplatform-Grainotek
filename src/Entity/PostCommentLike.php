<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use App\Controller\PostLikeController;
use App\Repository\PostCommentLikeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PostCommentLikeRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new Patch(),
        new Delete(),
        new \ApiPlatform\Metadata\Post(
            controller: PostLikeController::class,
            openapiContext: [
                'summary' => 'Like or unlike a post comment',
                'description' => 'If user has already liked, it will be unliked. If not, it will be liked.'
            ]
        )
    ],
    normalizationContext: ["groups" => ["like:read"]],
    denormalizationContext: ["groups" => ["like:write"]],
)]
class PostCommentLike
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['comment:read', 'comment:write', 'like:read', 'like:write'])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['comment:read', 'comment:write', 'like:read', 'like:write'])]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'postCommentLikes')]
    #[Groups(['comment:write', 'like:read', 'like:write'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?PostComment $postcomment = null;

    #[ORM\Column]
    #[Groups(['comment:read', 'comment:write', 'like:read'])]
    private ?bool $liked = null;

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

    public function getPostcomment(): ?PostComment
    {
        return $this->postcomment;
    }

    public function setPostcomment(?PostComment $postcomment): static
    {
        $this->postcomment = $postcomment;

        return $this;
    }

    public function isLiked(): ?bool
    {
        return $this->liked;
    }

    public function setLiked(bool $liked): static
    {
        $this->liked = $liked;

        return $this;
    }
}
