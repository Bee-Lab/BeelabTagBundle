<?php

namespace Beelab\TagBundle\Tests\Entity;

use Beelab\TagBundle\Test\Entity;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 */
final class AbstractTaggableTest extends TestCase
{
    public function testHasTag(): void
    {
        $tag = $this->getMockBuilder('Beelab\TagBundle\Tag\TagInterface')->getMock();
        $entity = new Entity();
        $entity->addTag($tag);
        $this->assertTrue($entity->hasTag($tag));
    }

    public function testRemoveTag(): void
    {
        $tag = $this->getMockBuilder('Beelab\TagBundle\Tag\TagInterface')->getMock();
        $entity = new Entity();
        $entity->addTag($tag);
        $entity->removeTag($tag);
        $this->assertFalse($entity->hasTag($tag));
    }

    public function testGetTags(): void
    {
        $tag = $this->getMockBuilder('Beelab\TagBundle\Tag\TagInterface')->getMock();
        $entity = new Entity();
        $entity->addTag($tag);
        $this->assertCount(1, $entity->getTags());
    }

    public function testGetTagsText(): void
    {
        $tag = $this->getMockBuilder('Beelab\TagBundle\Tag\TagInterface')->getMock();
        $entity = new Entity();
        $entity->setTagsText('foo, bar, baz');
        $this->assertEquals('', $entity->getTagsText());
    }

    public function testGetTagNames(): void
    {
        $tag = $this->getMockBuilder('Beelab\TagBundle\Tag\TagInterface')->getMock();
        $entity = new Entity();
        $this->assertEquals([], $entity->getTagNames());
    }
}
