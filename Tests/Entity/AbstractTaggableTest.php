<?php

namespace Beelab\TagBundle\Tests\Entity;

use Beelab\TagBundle\Tag\TagInterface;
use Beelab\TagBundle\Test\Entity;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 */
final class AbstractTaggableTest extends TestCase
{
    public function testHasTag(): void
    {
        /** @var TagInterface&\PHPUnit\Framework\MockObject\MockObject $tag */
        $tag = $this->createMock(TagInterface::class);
        $entity = new Entity();
        $entity->addTag($tag);
        $this->assertTrue($entity->hasTag($tag));
    }

    public function testRemoveTag(): void
    {
        /** @var TagInterface&\PHPUnit\Framework\MockObject\MockObject $tag */
        $tag = $this->createMock(TagInterface::class);
        $entity = new Entity();
        $entity->addTag($tag);
        $entity->removeTag($tag);
        $this->assertFalse($entity->hasTag($tag));
    }

    public function testGetTags(): void
    {
        /** @var TagInterface&\PHPUnit\Framework\MockObject\MockObject $tag */
        $tag = $this->createMock(TagInterface::class);
        $entity = new Entity();
        $entity->addTag($tag);
        $this->assertCount(1, $entity->getTags());
    }

    public function testGetTagsText(): void
    {
        /** @var TagInterface&\PHPUnit\Framework\MockObject\MockObject $tag */
        $tag = $this->createMock(TagInterface::class);
        $entity = new Entity();
        $entity->setTagsText('foo, bar, baz');
        $this->assertEquals('', $entity->getTagsText());
    }

    public function testGetTagNames(): void
    {
        /** @var TagInterface&\PHPUnit\Framework\MockObject\MockObject $tag */
        $tag = $this->createMock(TagInterface::class);
        $entity = new Entity();
        $this->assertEquals([], $entity->getTagNames());
    }
}
