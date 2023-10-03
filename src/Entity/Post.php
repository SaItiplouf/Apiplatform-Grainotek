<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use App\Controller\CreateImageActionController;
use App\Controller\UserPostsController;
use App\Repository\PostRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\PrePersist;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ApiResource(
    operations: [
        new \ApiPlatform\Metadata\Post(
            openapiContext: [
                'summary' => 'Ajouter un post',
                'description' => 'Permet d\'ajouter un nouveau post.',
                'tags' => ['Post'],
                'x-logo' => ['url' => 'https://cdn-icons-png.flaticon.com/512/902/902686.png'],
            ]
        ),
        new Get(
            openapiContext: [
                'summary' => 'Obtenir les détails d\'un post',
                'description' => 'Permet d\'obtenir les détails d\'un post spécifique.',
                'x-logo' => ['url' => 'https://exemple-url-image.com/logo-details.png'],
            ]
        ),
        new GetCollection(
            openapiContext: [
                'summary' => 'Lister tous les posts',
                'description' => 'Permet d\'obtenir une liste de tous les posts.',
                'x-logo' => ['url' => 'https://exemple-url-image.com/logo-liste.png'],
            ]
        ),
        new Delete(
            openapiContext: [
                'summary' => 'Supprimer un post',
                'description' => 'Permet de supprimer un post spécifique.',
                'x-logo' => ['url' => 'https://exemple-url-image.com/logo-supprimer.png'],
            ],
            security: "is_granted('ROLE_ADMIN') or object.getUser() == user"
        ),
        new GetCollection(
            uriTemplate: '/users/{id}/posts',
            controller: UserPostsController::class,
            openapiContext: [
                'summary' => 'Lister les posts d\'un utilisateur',
                'description' => 'Permet d\'obtenir une liste des posts d\'un utilisateur spécifique.',
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
            filters: ["order"]
        )
    ],
    normalizationContext: ['groups' => ['read:Post']],
    paginationEnabled: true,
    paginationItemsPerPage: 6,
)]
#[ApiFilter(DateFilter::class, properties: ['createdAt'])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt'], arguments: ['orderParameterName' => 'order'])]
#[ORM\HasLifecycleCallbacks]
class Post
{


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:Post', 'trade:read', "comment:write", 'trade:write', 'write:Room', 'read:Room', 'comment:read', 'read:Review', 'write:Review'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[ApiProperty(required: true)]
    #[Groups(['read:Post', 'trade:read', 'trade:write', "comment:write", 'write:Room', 'read:Room', 'comment:read', 'read:Review', 'write:Review'])]
    private string $name;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    #[ApiProperty(required: true)]
    #[Groups(['read:Post', 'trade:write', 'trade:read', "comment:write", 'write:Room'])]
    private ?User $user;

    #[ORM\Column(length: 255)]
    #[ApiProperty(required: true)]
    #[Groups(['read:Post', 'trade:read', 'trade:write', "comment:write", 'write:Room'])]
    private string $content;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read:Post', 'trade:read', 'trade:write', 'write:Room'])]
    private ?string $location = null;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: PostImage::class, cascade: ['remove'])]
    #[Groups(['read:Post', 'trade:read', 'trade:write', 'write:Room', 'read:Room', 'read:Review', 'write:Review'])]
    private Collection $images;

    #[ORM\Column]
    #[ApiProperty(readable: true, writable: false)]
    #[Groups(['read:Post', 'trade:read', 'trade:write', 'write:Room'])]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: PostComment::class, orphanRemoval: true)]
    private Collection $postComments;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: UserReview::class)]
    private Collection $userReviews;


    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->postComments = new ArrayCollection();
        $this->userReviews = new ArrayCollection();
    }

    public function getImages(): Collection
    {
        return $this->images;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

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

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): static
    {
        $this->location = $location;

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

    /**
     * @return Collection<int, PostComment>
     */
    public function getPostComments(): Collection
    {
        return $this->postComments;
    }

    public function addPostComment(PostComment $postComment): static
    {
        if (!$this->postComments->contains($postComment)) {
            $this->postComments->add($postComment);
            $postComment->setPost($this);
        }

        return $this;
    }

    public function removePostComment(PostComment $postComment): static
    {
        if ($this->postComments->removeElement($postComment)) {
            // set the owning side to null (unless already changed)
            if ($postComment->getPost() === $this) {
                $postComment->setPost(null);
            }
        }

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
            $userReview->setPost($this);
        }

        return $this;
    }

    public function removeUserReview(UserReview $userReview): static
    {
        if ($this->userReviews->removeElement($userReview)) {
            // set the owning side to null (unless already changed)
            if ($userReview->getPost() === $this) {
                $userReview->setPost(null);
            }
        }

        return $this;
    }
}
