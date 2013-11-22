<?php

namespace Beelab\TagBundle\Tag;

interface TaggableInterface
{
    /**
     * Add tag
     *
     * @param Tag $tag
     */
    public function addTag(TagInterface $tag);

    /**
     * Get names of tags
     *
     * @return array
     */
    public function getTagNames();

    /**
     * Get tags
     *
     * @return array|ArrayCollection
     */
    public function getTags();

    /**
     * Has tag
     *
     * @param  Tag     $tag
     * @return boolean
     */
    public function hasTag(TagInterface $tag);

    /**
     * Remove tag
     *
     * @param Tag $tag
     */
    public function removeTag(TagInterface $tag);
}