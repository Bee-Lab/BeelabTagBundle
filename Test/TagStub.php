<?php

namespace Beelab\TagBundle\Test;

use Beelab\TagBundle\Tag\TagInterface;

/**
 * A stub of a Tag class.
 */
class TagStub implements TagInterface
{
    /**
     * Set name.
     *
     * @param string $name
     */
    public function setName($name)
    {
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return 'a name';
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getTags()
    {
        return 'a name';
    }
}
