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
     *                      Override this property in your Entity with definition of ManyToMany relation
     */
    protected $tags;

    /**
     * @var string
     */
    protected $tagsText;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function addTag(TagInterface $tag)
    {
        $this->tags[] = $tag;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeTag(TagInterface $tag)
    {
        $this->tags->removeElement($tag);
    }

    /**
     * {@inheritdoc}
     */
    public function hasTag(TagInterface $tag)
    {
        return $this->tags->contains($tag);
    }

    /**
     * {@inheritdoc}
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * {@inheritdoc}
     */
    public function getTagNames()
    {
        return empty($this->tagsText) ? [] : array_map('trim', explode(',', $this->tagsText));
    }

    /**
     * Set tags text
     * Override this method in your Entity and update a field here.
     *
     * @param string $tagsText
     *
     * @return TaggableInterface
     */
    public function setTagsText($tagsText)
    {
        $this->tagsText = $tagsText;

        return $this;
    }

    /**
     * Get tags text.
     *
     * @return string
     */
    public function getTagsText()
    {
        $this->tagsText = implode(', ', $this->tags->toArray());

        return $this->tagsText;
    }
}
