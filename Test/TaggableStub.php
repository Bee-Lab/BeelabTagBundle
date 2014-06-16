<?php

namespace Beelab\TagBundle\Test;

use Beelab\TagBundle\Tag\TagInterface;
use Beelab\TagBundle\Tag\TaggableInterface;

/**
 * A stub of a Taggable class
 */
class TaggableStub implements TaggableInterface
{
    /**
     * Add tag
     *
     * @param Tag $tag
     */
    public function addTag(TagInterface $tag)
    {
    }

    /**
     * Get names of tags
     *
     * @return array
     */
    public function getTagNames()
    {
        return array('foo', 'bar');
    }

    /**
     * Get tags
     *
     * @return array|ArrayCollection
     */
    public function getTags()
    {
        return array(new TagStub());
    }

    /**
     * Has tag
     *
     * @param  Tag     $tag
     * @return boolean
     */
    public function hasTag(TagInterface $tag)
    {
    }

    /**
     * Remove tag
     *
     * @param Tag $tag
     */
    public function removeTag(TagInterface $tag)
    {
    }
}
