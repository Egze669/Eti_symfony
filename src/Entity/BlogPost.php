<?php

namespace App\Entity;

use App\Repository\BlogPostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BlogPostRepository::class)]
class BlogPost
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $title;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\Column(type: 'datetime')]
    private $created_at;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $additional_links;

    #[ORM\ManyToOne(targetEntity: BlogCategory::class, inversedBy: 'post')]
    #[ORM\JoinColumn(nullable: false)]
    private $category;

    #[ORM\Column(type: 'text')]
    private $content;

    #[ORM\OneToMany(mappedBy: 'comment', targetEntity: PostComment::class)]
    private $comment;

    public function __construct()
    {
        $this->comment = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTime $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getAdditionalLinks(): ?string
    {
        return $this->additional_links;
    }

    public function setAdditionalLinks(?string $additional_links): self
    {
        $this->additional_links = $additional_links;

        return $this;
    }

    public function getCategory(): ?BlogCategory
    {
        return $this->category;
    }

    public function setCategory(?BlogCategory $category): self
    {
        $this->category = $category;

        return $this;
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
//    public function __toString()
//    {
//        return $this->created_at;
//    }

/**
 * @return Collection<int, PostComment>
 */
public function getComment(): Collection
{
    return $this->comment;
}

public function addComment(PostComment $comment): self
{
    if (!$this->comment->contains($comment)) {
        $this->comment[] = $comment;
        $comment->setComment($this);
    }

    return $this;
}

public function removeComment(PostComment $comment): self
{
    if ($this->comment->removeElement($comment)) {
        // set the owning side to null (unless already changed)
        if ($comment->getComment() === $this) {
            $comment->setComment(null);
        }
    }

    return $this;
}
    public function __toString()
    {
        return $this->title;
    }
}
