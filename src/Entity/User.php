<?php

namespace App\Entity;

use ApiPlatform\Metadata\Patch;
use ApiPlatform\OpenApi\Model;
use App\Controller\CreateUserController;
use App\Controller\PatchUserProfilePictureController;
use ArrayObject;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\PatchUserController;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ApiResource(
    operations: [
        new Patch(
            controller: PatchUserController::class,
            openapiContext: array(
                'summary' => 'Mettre à jour le user',
                'description' => "Permet de mettre à jour certains champs de l'utilisateur",
            ),
            normalizationContext: ['groups' => ['read:Patch']],
            denormalizationContext: ['groups' => ['write:Patch']],
            deserialize: false
        ),
        new Post(
            uriTemplate: '/users/{id}/picture',
            controller: PatchUserProfilePictureController::class,
            openapiContext: [
                'summary' => 'Mettre à jour la photo de profil de l\'utilisateur',
                'description' => "Permet de mettre à jour certains champs de l'utilisateur",
                'requestBody' => [
                    'description' => 'Picture to be uploaded',
                    'content' => [
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'pictureFile' => [
                                        'type' => 'string',
                                        'format' => 'binary'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'responses' => [
                    '200' => [
                        'description' => 'User profile picture updated successfully'
                    ],
                    '400' => [
                        'description' => 'Bad request, no picture provided'
                    ]
                ]
            ],
            deserialize: false
        ),
        new Post(
            uriTemplate: '/register',
            controller: CreateUserController::class,
            openapiContext: [
                'summary' => 'Inscrire un user',
                'description' => 'Enregistrer un nouvel utilisateur en base de donnée',
            ],
            openapi: new Model\Operation(
                requestBody: new Model\RequestBody(
                    content: new ArrayObject([
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'email' => [
                                        "type" => "string",
                                        "description" => "E-mail de l'utilisateur"
                                    ],
                                    'password' => [
                                        "type" => "string",
                                        "format" => "password",
                                        "description" => "Mot de passe"
                                    ],
                                    'username' => [
                                        "type" => "string",
                                        "description" => "Nom d'utilisateur / Pseudo"
                                    ],
                                    'pictureFile' => [
                                        "type" => "string",
                                        "format" => "binary",
                                        "description" => "Fichier image"
                                    ],
                                ],
                                'required' => ['email', 'password']
                            ]
                        ]
                    ])
                )
            ),
            deserialize: false
        ),
        new GetCollection(
            uriTemplate: '/users',
            openapiContext: [
                'summary' => 'Récupérer la liste de tout les utilisateurs',
                'description' => 'Récupérer la liste de tout les utilisateurs',
            ],
            security: 'is_granted("ROLE_USER")'
        ),
        new Get(
            uriTemplate: '/users/{id}',
            openapiContext: [
                'summary' => "Récupérer les infos d'un seul utilisateur",
                'description' => "Récupérer les infos d'un seul utilisateur",
            ],
        )
    ],
    normalizationContext: ['groups' => ['read:User']],
    denormalizationContext: ['groups' => ['write:User']]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:User', 'write:User', 'comment:write', 'like:write', "like:read", 'comment:read', 'trade:read', 'read:Post', 'read:Room', 'read:Message', 'trade:write', 'write:Room', 'read:Review', 'write:Review'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['read:User', 'comment:read', "like:read", "like:write", 'comment:write', 'trade:read', 'read:Message', 'read:Room', 'write:User', 'read:Post', 'read:Patch', 'write:Patch', 'trade:write', 'write:Room', 'read:Review', 'write:Review'])]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups(['read:User', 'read:Patch', 'read:Room'])]
    private array $roles = ["ROLE_USER"];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups(['write:User', 'write:Patch'])]
    private ?string $password = null;


    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read:User', 'read:Message'])]
    private ?string $picture = null;

    #[Vich\UploadableField(mapping: "profile_picture", fileNameProperty: "picture")]
    #[Groups(['write:User'])]
    private $pictureFile;

    #[ORM\Column(length: 255, unique: true, nullable: true)]
    #[Groups(['write:User', 'trade:read', "like:read", "like:write", 'comment:write', 'comment:read', 'read:Message', 'read:Room', 'create:User', 'read:User', 'read:Patch', 'write:Patch', 'trade:write', 'write:Room', 'read:Review', 'write:Review'])]
    private ?string $username = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read:User', 'trade:read', 'comment:write', "like:read", 'comment:read', 'read:Message', 'read:Room', 'read:Post', 'read:Trade', 'trade:write', 'write:Room', 'read:Review', 'write:Review'])]
    private ?string $pictureUrl = null;

    #[ORM\OneToMany(mappedBy: 'applicant', targetEntity: Trade::class)]
    private Collection $trades;

    #[ORM\ManyToMany(targetEntity: Room::class, mappedBy: 'users')]
    private Collection $rooms;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserReview::class, orphanRemoval: true)]
    #[Groups(['read:Post'])]
    private Collection $userReviews;
    #[ORM\OneToMany(mappedBy: 'targetedUser', targetEntity: UserReview::class, orphanRemoval: true)]
    #[Groups(['read:Post'])]
    private Collection $userReviewsWhereUserIsTargeted;

    public function __construct()
    {
        $this->trades = new ArrayCollection();
        $this->rooms = new ArrayCollection();
        $this->userReviews = new ArrayCollection();
        $this->userReviewsWhereUserIsTargeted = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getPictureUrl(): ?string
    {
        return $this->pictureUrl;
    }

    /**
     * @param string|null $pictureUrl
     */
    public function setPictureUrl(?string $pictureUrl): void
    {
        $this->pictureUrl = $pictureUrl;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function erasePictureFromEntity(): void
    {
        $this->setPicture(null);
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    public function getPictureFile()
    {
        return $this->pictureFile;
    }

    public function setPictureFile($pictureFile): self
    {
        $this->pictureFile = $pictureFile;
        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Trade>
     */
    public function getTrades(): Collection
    {
        return $this->trades;
    }

    public function addTrade(Trade $trade): static
    {
        if (!$this->trades->contains($trade)) {
            $this->trades->add($trade);
            $trade->setApplicant($this);
        }

        return $this;
    }

    public function removeTrade(Trade $trade): static
    {
        if ($this->trades->removeElement($trade)) {
            // set the owning side to null (unless already changed)
            if ($trade->getApplicant() === $this) {
                $trade->setApplicant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Room>
     */
    public function getRooms(): Collection
    {
        return $this->rooms;
    }

    public function addRoom(Room $room): static
    {
        if (!$this->rooms->contains($room)) {
            $this->rooms->add($room);
            $room->addUser($this);
        }

        return $this;
    }

    public function removeRoom(Room $room): static
    {
        if ($this->rooms->removeElement($room)) {
            $room->removeUser($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, UserReview>
     */
    public function getUserReviewsWhereUserIsTargeted(): Collection
    {
        $targetedUserReviews = new ArrayCollection();

        foreach ($this->userReviewsWhereUserIsTargeted as $userReview) {
            $targetedUserReviews->add($userReview);
        }

        return $targetedUserReviews;
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
            $userReview->setUser($this);
        }

        return $this;
    }

    public function removeUserReview(UserReview $userReview): static
    {
        if ($this->userReviews->removeElement($userReview)) {
            // set the owning side to null (unless already changed)
            if ($userReview->getUser() === $this) {
                $userReview->setUser(null);
            }
        }

        return $this;
    }

}