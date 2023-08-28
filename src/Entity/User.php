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
            ],
            openapi: new Model\Operation(
                requestBody: new Model\RequestBody(
                    content: new ArrayObject([
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'pictureFile' => [
                                        "type" => "string",
                                        "format" => "binary",
                                        "description" => "Fichier image"
                                    ],
                                    'required' => 'pictureFile'
                                ],
                            ]
                        ]
                    ])
                )
            ),
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
    #[Groups(['read:User', 'read:Post'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['read:User', 'write:User', 'read:Post', 'read:Patch', 'write:Patch'])]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups(['read:User', 'read:Patch', 'write:Patch'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups(['write:User', 'write:Patch', 'read:Patch'])]
    private ?string $password = null;


    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read:User'])]
    private ?string $picture = null;

    #[Vich\UploadableField(mapping: "profile_picture", fileNameProperty: "picture")]
    #[Groups(['write:User'])]
    private $pictureFile;

    #[ORM\Column(length: 255, unique: true, nullable: true)]
    #[Groups(['write:User', 'create:User', 'read:User', 'read:Patch', 'write:Patch'])]
    private ?string $username = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read:User'])]
    private ?string $pictureUrl = null;

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
}