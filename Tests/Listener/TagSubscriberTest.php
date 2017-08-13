<?php

namespace Beelab\TagBundle\Tests\Listener;

use Beelab\TagBundle\Listener\TagSubscriber;
use Beelab\TagBundle\Test\NonTaggableStub;
use Beelab\TagBundle\Test\TaggableStub;
use Beelab\TagBundle\Test\TaggableStub2;
use Beelab\TagBundle\Test\TaggableStub3;
use Beelab\TagBundle\Test\TagStub;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 */
class TagSubscriberTest extends TestCase
{
    /**
     * @expectedException \Doctrine\Common\Persistence\Mapping\MappingException
     */
    public function testNonexistentClass()
    {
        $subscriber = new TagSubscriber('ClassDoesNotExist');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidClass()
    {
        $subscriber = new TagSubscriber(NonTaggableStub::class);
    }

    public function testGetSubscribedEvents()
    {
        $tag = $this->getMockBuilder('Beelab\TagBundle\Tag\TagInterface')->getMock();
        $subscriber = new TagSubscriber(get_class($tag));

        $this->assertContains('onFlush', $subscriber->getSubscribedEvents());
    }

    public function testOnFlush()
    {
        $tag = $this->getMockBuilder('Beelab\TagBundle\Tag\TagInterface')->getMock();
        $args = $this->getMockBuilder(OnFlushEventArgs::class)->disableOriginalConstructor()->getMock();
        $manager = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')->disableOriginalConstructor()->getMock();
        $uow = $this->getMockBuilder('Doctrine\ORM\UnitOfWork')->disableOriginalConstructor()->getMock();
        $metadata = $this->getMockBuilder(ClassMetadata::class)->disableOriginalConstructor()->getMock();

        $args->expects($this->once())->method('getEntityManager')->will($this->returnValue($manager));
        $manager->expects($this->once())->method('getUnitOfWork')->will($this->returnValue($uow));
        $manager->expects($this->any())->method('getRepository')->will($this->returnValue($repo));
        $manager->expects($this->any())->method('getClassMetadata')->will($this->returnValue($metadata));
        $uow
            ->expects($this->once())
            ->method('getScheduledEntityInsertions')
            ->will($this->returnValue([new TaggableStub(), new NonTaggableStub()]))
        ;
        $uow
            ->expects($this->once())
            ->method('getScheduledEntityUpdates')
            ->will($this->returnValue([new TaggableStub2()]))
        ;
        $uow->expects($this->never())->method('getScheduledEntityDeletions');

        $subscriber = new TagSubscriber(get_class($tag));
        $subscriber->onFlush($args);
    }

    public function testOnFlushEntityWithoutTagsUpdate()
    {
        $tag = $this->getMockBuilder('Beelab\TagBundle\Tag\TagInterface')->getMock();
        $args = $this->getMockBuilder(OnFlushEventArgs::class)->disableOriginalConstructor()->getMock();
        $manager = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $uow = $this->getMockBuilder('Doctrine\ORM\UnitOfWork')->disableOriginalConstructor()->getMock();
        $metadata = $this->getMockBuilder(ClassMetadata::class)->disableOriginalConstructor()->getMock();

        $args->expects($this->once())->method('getEntityManager')->will($this->returnValue($manager));
        $manager->expects($this->once())->method('getUnitOfWork')->will($this->returnValue($uow));
        $manager->expects($this->any())->method('getClassMetadata')->will($this->returnValue($metadata));
        $uow
            ->expects($this->once())
            ->method('getScheduledEntityInsertions')
            ->will($this->returnValue([]))
        ;
        $uow
            ->expects($this->once())
            ->method('getScheduledEntityUpdates')
            ->will($this->returnValue([new TaggableStub3()]))
        ;
        $uow->expects($this->never())->method('getScheduledEntityDeletions');

        $subscriber = new TagSubscriber(get_class($tag));
        $subscriber->onFlush($args);
    }

    public function testOnFlushEntityWithoutTagsInsert()
    {
        $tag = $this->getMockBuilder('Beelab\TagBundle\Tag\TagInterface')->getMock();
        $args = $this->getMockBuilder(OnFlushEventArgs::class)->disableOriginalConstructor()->getMock();
        $manager = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $uow = $this->getMockBuilder('Doctrine\ORM\UnitOfWork')->disableOriginalConstructor()->getMock();
        $metadata = $this->getMockBuilder(ClassMetadata::class)->disableOriginalConstructor()->getMock();

        $args->expects($this->once())->method('getEntityManager')->will($this->returnValue($manager));
        $manager->expects($this->once())->method('getUnitOfWork')->will($this->returnValue($uow));
        $manager->expects($this->any())->method('getClassMetadata')->will($this->returnValue($metadata));
        $uow
            ->expects($this->once())
            ->method('getScheduledEntityInsertions')
            ->will($this->returnValue([new TaggableStub3()]))
        ;
        $uow
            ->expects($this->once())
            ->method('getScheduledEntityUpdates')
            ->will($this->returnValue([]))
        ;
        $uow->expects($this->never())->method('getScheduledEntityDeletions');

        $subscriber = new TagSubscriber(get_class($tag));
        $subscriber->onFlush($args);
    }

    public function testOnFlushWithPurge()
    {
        $tag = new TagStub();
        $args = $this->getMockBuilder(OnFlushEventArgs::class)->disableOriginalConstructor()->getMock();
        $manager = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $uow = $this->getMockBuilder('Doctrine\ORM\UnitOfWork')->disableOriginalConstructor()->getMock();

        $args->expects($this->once())->method('getEntityManager')->will($this->returnValue($manager));
        $manager->expects($this->once())->method('getUnitOfWork')->will($this->returnValue($uow));
        $uow->expects($this->once())->method('getScheduledEntityInsertions')->will($this->returnValue([]));
        $uow->expects($this->once())->method('getScheduledEntityUpdates')->will($this->returnValue([]));
        $uow
            ->expects($this->once())
            ->method('getScheduledEntityDeletions')
            ->will($this->returnValue([new TaggableStub()]))
        ;

        $subscriber = new TagSubscriber(get_class($tag), true);
        $subscriber->onFlush($args);
    }

    public function testSetTags()
    {
        $tag = $this->getMockBuilder('Beelab\TagBundle\Tag\TagInterface')->getMock();
        $args = $this->getMockBuilder(OnFlushEventArgs::class)->disableOriginalConstructor()->getMock();
        $manager = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $uow = $this->getMockBuilder('Doctrine\ORM\UnitOfWork')->disableOriginalConstructor()->getMock();

        $args->expects($this->once())->method('getEntityManager')->will($this->returnValue($manager));
        $manager->expects($this->once())->method('getUnitOfWork')->will($this->returnValue($uow));
        // TODO create some stubs of taggable entities and non-taggable entities...
        $uow->expects($this->once())->method('getScheduledEntityInsertions')->will($this->returnValue([$tag]));
        $uow->expects($this->once())->method('getScheduledEntityUpdates')->will($this->returnValue([]));
        $uow->expects($this->once())->method('getScheduledEntityDeletions')->will($this->returnValue([]));

        $subscriber = new TagSubscriber(get_class($tag), true);
        $subscriber->onFlush($args);
    }
}
