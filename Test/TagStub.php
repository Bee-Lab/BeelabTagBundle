<?php

namespace Beelab\TagBundle\Test;

use Beelab\TagBundle\Tag\TagInterface;

/**
 * A stub of a Tag class.
 */
class TagStub implements TagInterface
{
    public function setName(?string $name): void
    {
    }

    public function getName(): ?string
    {
        return 'a name';
    }

    public function getTags(): ?string
    {
        return 'a name';
    }
}
