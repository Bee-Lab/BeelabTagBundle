<?php

namespace Beelab\TagBundle\Tag;

interface TagInterface
{
    public function setName(?string $name): void;

    public function getName(): ?string;
}
