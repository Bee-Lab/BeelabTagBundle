<?php

namespace Beelab\TagBundle\Tests\Entity;

use Beelab\TagBundle\Test\Entity;

/**
 * @group unit
 */
class AbstractTaggableTest extends \PHPUnit_Framework_TestCase
{
    public function testHasTag()
    {
        $tag = $this->getMockBuilder('Beelab\TagBundle\Tag\TagInterface')->getMock();
        $entity = new Entity();
        $entity->addTag($tag);
        $this->assertTrue($entity->hasTag($tag));
    }

    public function testRemoveTag()
    {
        $tag = $this->getMockBuilder('Beelab\TagBundle\Tag\TagInterface')->getMock();
        $entity = new Entity();
        $entity->addTag($tag);
        $entity->removeTag($tag);
        $this->assertFalse($entity->hasTag($tag));
    }

    public function testGetTags()
    {
        $tag = $this->getMockBuilder('Beelab\TagBundle\Tag\TagInterface')->getMock();
        $entity = new Entity();
        $entity->addTag($tag);
        $this->assertCount(1, $entity->getTags());
    }

    public function testGetTagsText()
    {
        $tag = $this->getMockBuilder('Beelab\TagBundle\Tag\TagInterface')->getMock();
        $entity = new Entity();
        $entity->setTagsText('foo, bar, baz');
        $this->assertEquals('', $entity->getTagsText());
    }

    public function testGetTagNames()
    {
        $tag = $this->getMockBuilder('Beelab\TagBundle\Tag\TagInterface')->getMock();
        $entity = new Entity();
        $this->assertEquals([], $entity->getTagNames());
    }
}
