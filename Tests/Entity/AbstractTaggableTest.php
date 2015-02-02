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
        $tag = $this->getMock('Beelab\TagBundle\Tag\TagInterface');
        $entity = new Entity();
        $entity->addTag($tag);
        $this->assertTrue($entity->hasTag($tag));
    }

    public function testRemoveTag()
    {
        $tag = $this->getMock('Beelab\TagBundle\Tag\TagInterface');
        $entity = new Entity();
        $entity->addTag($tag);
        $entity->removeTag($tag);
        $this->assertFalse($entity->hasTag($tag));
    }

    public function testGetTags()
    {
        $tag = $this->getMock('Beelab\TagBundle\Tag\TagInterface');
        $entity = new Entity();
        $entity->addTag($tag);
        $this->assertCount(1, $entity->getTags());
    }

    public function testGetTagsText()
    {
        $tag = $this->getMock('Beelab\TagBundle\Tag\TagInterface');
        #$tag->expects($this->once())->method('__toString')->will($this->returnValue('foo'));
        $entity = new Entity();
        $entity->setTagsText('foo, bar, baz');
        $this->assertEquals('', $entity->getTagsText());
    }

    public function testGetTagNames()
    {
        $tag = $this->getMock('Beelab\TagBundle\Tag\TagInterface');
        $entity = new Entity();
        $this->assertEquals([], $entity->getTagNames());
    }
}
