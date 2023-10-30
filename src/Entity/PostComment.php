<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Odm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use App\Controller\GetAllCommentsFromPostController;
use App\Repository\PostCommentRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\PrePersist;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: PostCommentRepository::class)]
#[ApiResource(
    operations: [
        new Delete(),
        new \ApiPlatform\Metadata\Post(
            openapiContext: [
                'requestBody' => [
                    'description' => 'Create a new post comment',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'user' => [
                                        'type' => 'object',
                                        'description' => 'The user object',
                                    ],
                                    'post' => [
                                        'type' => 'object',
                                        'description' => 'The post object',
                                    ],
                                    'content' => [
                                        'type' => 'string',
                                        'description' => 'The content of the comment',
                                    ]
                                ],
                                'required' => ['user', 'post', 'content']
                            ]
                        ]
                    ]
                ]
            ]),
        new Patch(),
        new GetCollection(
            uriTemplate: '/post_comments/post/{id}',
            controller: GetAllCommentsFromPostController::class,
            openapiContext: [
                'summary' => 'Lister les commentaires d\'un post',
                'description' => 'Permet d\'obtenir une liste des commentaires d\'un post.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'in' => 'path',
                        'required' => true,
                        'description' => 'ID du post',
                        'schema' => [
                            'type' => 'integer'
                        ]
                    ],
                    [
                        'name' => 'page',
                        'in' => 'query',
                        'required' => false,
                        'description' => 'Numéro de la page',
                        'schema' => [
                            'type' => 'integer',
                            'default' => 1
                        ]
                    ],
                ],
            ],
            paginationEnabled: true,
            paginationItemsPerPage: 3
        ),
    ],
    normalizationContext: ["groups" => ["comment:read"]],
    denormalizationContext: ["groups" => ["comment:write"]],

)]
#[ApiFilter(OrderFilter::class, properties: ['createdAt' => 'DESC'])]
class PostComment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['comment:read', "like:write", "like:read", 'comment:write'])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['comment:read', 'comment:write', "like:read", "like:write", 'read:User', 'write:User'])]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'postComments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['comment:write', 'comment:read', 'like:read'])]
    private ?Post $post = null;

    #[ORM\Column(length: 255)]
    #[Groups(['comment:read', 'comment:write', "like:read", "like:write",])]
    private ?string $content = null;

    #[ORM\OneToMany(mappedBy: 'postcomment', targetEntity: PostCommentLike::class, orphanRemoval: true)]
    #[Groups(['comment:read'])]
    private Collection $postCommentLikes;

    #[ORM\Column]
    #[ApiProperty(readable: true, writable: false)]
    #[Groups(['comment:read', 'comment:write'])]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->postCommentLikes = new ArrayCollection();
    }

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

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): static
    {
        $this->post = $post;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return Collection<int, PostCommentLike>
     */
    public function getPostCommentLikes(): Collection
    {
        return $this->postCommentLikes;
    }

    public function addPostCommentLike(PostCommentLike $postCommentLike): static
    {
        if (!$this->postCommentLikes->contains($postCommentLike)) {
            $this->postCommentLikes->add($postCommentLike);
            $postCommentLike->setPostcomment($this);
        }

        return $this;
    }

    public function removePostCommentLike(PostCommentLike $postCommentLike): static
    {
        if ($this->postCommentLikes->removeElement($postCommentLike)) {
            // set the owning side to null (unless already changed)
            if ($postCommentLike->getPostcomment() === $this) {
                $postCommentLike->setPostcomment(null);
            }
        }

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
}
