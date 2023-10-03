<?php

namespace App\Entity;

use ApiPlatform\Metadata\Delete;
use App\Controller\CreateImageActionController;
use ArrayObject;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post as ApiPost;
use ApiPlatform\OpenApi\Model;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ORM\Entity]
#[ApiResource(
    types: ['https://schema.org/MediaObject'],
    operations: [
        new Get(
            openapiContext: [
                'summary' => 'Récupérer une image liée à un post'
            ]
        ),
        new Delete(
            openapiContext: [
                'summary' => 'Supprimer une image liée à un post'
            ]
        ),
        new ApiPost(
            controller: CreateImageActionController::class,
            openapiContext: [
                'summary' => 'Créer une postImage'
            ],
            openapi: new Model\Operation(
                requestBody: new Model\RequestBody(
                    content: new ArrayObject([
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'file1' => [
                                        "type" => "string",
                                        "format" => "binary"
                                    ],
                                    'file2' => [
                                        "type" => "string",
                                        "format" => "binary"
                                    ],
                                    'file3' => [
                                        "type" => "string",
                                        "format" => "binary"
                                    ],
                                    'file4' => [
                                        "type" => "string",
                                        "format" => "binary"
                                    ],
                                    'post_id' => [
                                        'type' => 'integer',
                                        'description' => "L'ID du post associé."
                                    ]
                                ],
                                'required' => ['post_id', 'file1']
                            ]
                        ]
                    ])
                )
            ),
            deserialize: false
        )
    ],
    normalizationContext: ['groups' => ['media_object:read']],
    denormalizationContext: ['groups' => ['media_object:write']]
)]
class PostImage
{
    #[ORM\Column(nullable: true)]
    public ?string $filePath = null;
    #[ApiProperty(types: ['https://schema.org/contentUrl'])]
    #[Groups(['media_object:read', 'read:Post', 'trade:read', 'read:Room'])]
    #[ORM\Column(nullable: true)]
    public ?string $contentUrl = null;

    #[Vich\UploadableField(mapping: "media_object", fileNameProperty: "filePath")]
    #[Assert\NotNull(groups: ['media_object_create'])]
    #[Groups(['media_object:write'])]
    private ?File $file = null;

    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    #[Groups(['read:Post'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: "images")]
    #[ORM\JoinColumn(name: 'post', referencedColumnName: 'id')]
    private ?Post $post;

    public function getId(): ?int
    {
        return $this->id;
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


    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(?string $filePath): void
    {
        $this->filePath = $filePath;
    }

    public function getContentUrl(): ?string
    {
        return $this->contentUrl;
    }

    public function setContentUrl(?string $contentUrl): void
    {
        $this->contentUrl = $contentUrl;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): void
    {
        $this->file = $file;
    }


}