<?php

namespace Beelab\TagBundle\Tag;

interface TaggableInterface
{
    public function addTag(TagInterface $tag): void;

    public function getTagNames(): array;

    public function getTags(): iterable;

    public function hasTag(TagInterface $tag): bool;

    public function removeTag(TagInterface $tag): void;
}
