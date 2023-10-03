<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\MarkMessagesAsReadController;
use App\Controller\UserRoomController;
use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/rooms',
            openapiContext: [
                'summary' => 'Créer une nouvelle room',
                'description' => 'Permet de créer une nouvelle room avec un nom optionnel.',
            ],
        ),
        new Get(
            openapiContext: [
                'summary' => 'Obtenir les détails d\'une room',
                'description' => 'Permet d\'obtenir les détails d\'une room spécifique.',
            ]
        ),
        new Post(
            uriTemplate: '/messages/mark-messages-as-read',
            controller: MarkMessagesAsReadController::class,
            openapiContext: [
                'summary' => 'Passer les messages comme lu',
                'description' => 'Recupère tout les messages de la room, compare au user envoyé et boucle tout les autres en false',
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'user' => [
                                        'type' => 'object',
                                        'required' => ['id'],  // Specify the required properties for the user object
                                        'properties' => [
                                            'id' => [
                                                'type' => 'integer',
                                                'description' => 'ID of the user',
                                            ],
                                        ],
                                    ],
                                    'room' => [
                                        'type' => 'object',
                                        'required' => ['id'],  // Specify the required properties for the room object
                                        'properties' => [
                                            'id' => [
                                                'type' => 'integer',
                                                'description' => 'ID of the room',
                                            ],
                                            // ... additional room properties if needed
                                        ],
                                    ],
                                ],
                                'required' => ['user', 'room'],  // Both user and room objects are required
                            ],
                        ],
                    ],
                ],
            ]
        ),
        new Delete(
            openapiContext: [
                'summary' => 'Supprimer une room',
                'description' => 'Permet de supprimer une room spécifique.',
            ],
            security: "is_granted('ROLE_ADMIN') or object.getUser() == user"
        ),
        new GetCollection(
            uriTemplate: '/users/{id}/rooms',
            controller: UserRoomController::class,
            openapiContext: [
                'summary' => 'Lister les rooms d\'un utilisateur',
                'description' => 'Permet d\'obtenir une liste des rooms d\'un utilisateur spécifique.',
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
    normalizationContext: ['groups' => ['read:Room']],
    denormalizationContext: ['groups' => ['write:Room']],
    mercure: true,
)]
class Room
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[Groups(['read:Room', 'tradePatch:write', 'write:Room', 'trade:write'])]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read:Room', 'write:Room', 'tradePatch:write', 'trade:read', 'trade:write', 'trade:write'])]
    private ?string $name = null;

    #[ORM\OneToOne(inversedBy: 'room', targetEntity: Trade::class, cascade: ['persist', 'remove'])]
    #[Groups(['read:Room', 'write:Room', 'tradePatch:write', 'read:Trade', 'trade:write', 'read:Message', 'trade:write'])]
    private ?Trade $trade = null;


    #[ORM\OneToMany(mappedBy: 'room', targetEntity: Message::class, orphanRemoval: true)]
    #[Groups(['read:Room', 'read:Message', 'trade:write'])] // Include Message properties in the same group
    private Collection $messages;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'rooms', cascade: ["persist"])]
    #[Groups(['read:Room', 'write:Room', 'read:User', 'read:Message', 'trade:read', 'trade:write', 'trade:write'])]
    private Collection $users;


    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $LastMessage = null;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

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

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setRoom($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getRoom() === $this) {
                $message->setRoom(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(?User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addRoom($this);  // Ajouter cette ligne
        }

        return $this;
    }

    public function removeUser(?User $user): static
    {
        $this->users->removeElement($user);

        return $this;
    }

    public function getLastMessage(): ?\DateTimeInterface
    {
        return $this->LastMessage;
    }

    public function setLastMessage(?\DateTimeInterface $LastMessage): static
    {
        $this->LastMessage = $LastMessage;

        return $this;
    }

}
