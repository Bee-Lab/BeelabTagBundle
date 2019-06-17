<?php

namespace Beelab\TagBundle\Test;

use Beelab\TagBundle\Tag\TaggableInterface;
use Beelab\TagBundle\Tag\TagInterface;

/**
 * A stub of a Taggable class.
 */
class TaggableStub implements TaggableInterface
{
    public function addTag(TagInterface $tag): void
    {
    }

    public function getTagNames(): array
    {
        return ['foo', 'bar'];
    }

    public function getTags(): iterable
    {
        return [new TagStub()];
    }

    public function removeTag(TagInterface $tag): void
    {
    }
}
