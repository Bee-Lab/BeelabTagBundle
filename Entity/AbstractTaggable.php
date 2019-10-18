<?php

namespace Beelab\TagBundle\Entity;

use Beelab\TagBundle\Tag\TagInterface;
use Beelab\TagBundle\Tag\TaggableInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Abstract Taggable class
 * You can extend this class in your Entity.
 */
abstract class AbstractTaggable implements TaggableInterface
{
    /**
     * @var ArrayCollection
     *
     * Override this property in your Entity with definition of ManyToMany relation.
     */
    protected $tags;

    /**
     * @var string
     */
    protected $tagsText;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    public function addTag(TagInterface $tag): void
    {
        $this->tags[] = $tag;
    }

    public function removeTag(TagInterface $tag): void
    {
        $this->tags->removeElement($tag);
    }

    public function hasTag(TagInterface $tag): bool
    {
        return $this->tags->contains($tag);
    }

    public function getTags(): iterable
    {
        return $this->tags;
    }

    public function getTagNames(): array
    {
        return empty($this->tagsText) ? [] : array_map('trim', explode(',', $this->tagsText));
    }

    /**
     * Override this method in your Entity and update a field here.
     *
     * @param string $tagsText
     */
    public function setTagsText(?string $tagsText): void
    {
        $this->tagsText = $tagsText;
    }

    public function getTagsText(): ?string
    {
        $this->tagsText = empty($this->tags) ? null : implode(', ', $this->tags->toArray());

        return $this->tagsText;
    }
}
