<?php

namespace App\Entity;

use App\Repository\PostCommentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostCommentRepository::class)]
class PostComment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'text')]
    private $content;

    #[ORM\Column(type: 'string', length: 255)]
    private $author;

    #[ORM\Column(type: 'datetime')]
    private $created_at;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $additional_images;

    #[ORM\ManyToOne(targetEntity: BlogPost::class, inversedBy: 'comment')]
    private $comment;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getAdditionalImages(): ?string
    {
        return $this->additional_images;
    }

    public function setAdditionalImages(?string $additional_images): self
    {
        $this->additional_images = $additional_images;

        return $this;
    }

    public function getComment(): ?BlogPost
    {
        return $this->comment;
    }

    public function setComment(?BlogPost $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
    public function __toString()
    {
        return $this->created_at;
    }
}
