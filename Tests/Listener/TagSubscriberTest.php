<?php

namespace Beelab\TagBundle\Tests\Listener;

use Beelab\TagBundle\Listener\TagSubscriber;
use Beelab\TagBundle\Test\NonTaggableStub;
use Beelab\TagBundle\Test\TaggableStub;
use Beelab\TagBundle\Test\TaggableStub2;
use Beelab\TagBundle\Test\TaggableStub3;
use Beelab\TagBundle\Test\TagStub;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 */
final class TagSubscriberTest extends TestCase
{
    public function testNonexistentClass(): void
    {
        $this->expectException(MappingException::class);

        $subscriber = new TagSubscriber('ClassDoesNotExist');
    }

    public function testInvalidClass(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $subscriber = new TagSubscriber(NonTaggableStub::class);
    }

    public function testGetSubscribedEvents(): void
    {
        $tag = $this->getMockBuilder('Beelab\TagBundle\Tag\TagInterface')->getMock();
        $subscriber = new TagSubscriber(\get_class($tag));

        $this->assertContains('onFlush', $subscriber->getSubscribedEvents());
    }

    public function testOnFlush(): void
    {
        $tag = $this->getMockBuilder('Beelab\TagBundle\Tag\TagInterface')->getMock();
        /** @var OnFlushEventArgs&\PHPUnit\Framework\MockObject\MockObject $args */
        $args = $this->getMockBuilder(OnFlushEventArgs::class)->disableOriginalConstructor()->getMock();
        $manager = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')->disableOriginalConstructor()->getMock();
        $uow = $this->getMockBuilder('Doctrine\ORM\UnitOfWork')->disableOriginalConstructor()->getMock();
        $metadata = $this->getMockBuilder(ClassMetadata::class)->disableOriginalConstructor()->getMock();

        $args->expects($this->once())->method('getEntityManager')->willReturn($manager);
        $manager->expects($this->once())->method('getUnitOfWork')->willReturn($uow);
        $manager->expects($this->any())->method('getRepository')->willReturn($repo);
        $manager->expects($this->any())->method('getClassMetadata')->willReturn($metadata);
        $uow
            ->expects($this->once())
            ->method('getScheduledEntityInsertions')
            ->willReturn([new TaggableStub(), new NonTaggableStub()])
        ;
        $uow
            ->expects($this->once())
            ->method('getScheduledEntityUpdates')
            ->willReturn([new TaggableStub2()])
        ;
        $uow->expects($this->never())->method('getScheduledEntityDeletions');

        $subscriber = new TagSubscriber(\get_class($tag));
        $subscriber->onFlush($args);
    }

    public function testOnFlushEntityWithoutTagsUpdate(): void
    {
        $tag = $this->getMockBuilder('Beelab\TagBundle\Tag\TagInterface')->getMock();
        /** @var OnFlushEventArgs&\PHPUnit\Framework\MockObject\MockObject $args */
        $args = $this->getMockBuilder(OnFlushEventArgs::class)->disableOriginalConstructor()->getMock();
        $manager = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $uow = $this->getMockBuilder('Doctrine\ORM\UnitOfWork')->disableOriginalConstructor()->getMock();
        $metadata = $this->getMockBuilder(ClassMetadata::class)->disableOriginalConstructor()->getMock();

        $args->expects($this->once())->method('getEntityManager')->willReturn($manager);
        $manager->expects($this->once())->method('getUnitOfWork')->willReturn($uow);
        $manager->expects($this->any())->method('getClassMetadata')->willReturn($metadata);
        $uow
            ->expects($this->once())
            ->method('getScheduledEntityInsertions')
            ->willReturn([])
        ;
        $uow
            ->expects($this->once())
            ->method('getScheduledEntityUpdates')
            ->willReturn([new TaggableStub3()])
        ;
        $uow->expects($this->never())->method('getScheduledEntityDeletions');

        $subscriber = new TagSubscriber(\get_class($tag));
        $subscriber->onFlush($args);
    }

    public function testOnFlushEntityWithoutTagsInsert(): void
    {
        $tag = $this->getMockBuilder('Beelab\TagBundle\Tag\TagInterface')->getMock();
        /** @var OnFlushEventArgs&\PHPUnit\Framework\MockObject\MockObject $args */
        $args = $this->getMockBuilder(OnFlushEventArgs::class)->disableOriginalConstructor()->getMock();
        $manager = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $uow = $this->getMockBuilder('Doctrine\ORM\UnitOfWork')->disableOriginalConstructor()->getMock();
        $metadata = $this->getMockBuilder(ClassMetadata::class)->disableOriginalConstructor()->getMock();

        $args->expects($this->once())->method('getEntityManager')->willReturn($manager);
        $manager->expects($this->once())->method('getUnitOfWork')->willReturn($uow);
        $manager->expects($this->any())->method('getClassMetadata')->willReturn($metadata);
        $uow
            ->expects($this->once())
            ->method('getScheduledEntityInsertions')
            ->willReturn([new TaggableStub3()])
        ;
        $uow
            ->expects($this->once())
            ->method('getScheduledEntityUpdates')
            ->willReturn([])
        ;
        $uow->expects($this->never())->method('getScheduledEntityDeletions');

        $subscriber = new TagSubscriber(\get_class($tag));
        $subscriber->onFlush($args);
    }

    public function testOnFlushWithPurge(): void
    {
        $tag = new TagStub();
        /** @var OnFlushEventArgs&\PHPUnit\Framework\MockObject\MockObject $args */
        $args = $this->getMockBuilder(OnFlushEventArgs::class)->disableOriginalConstructor()->getMock();
        $manager = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $uow = $this->getMockBuilder('Doctrine\ORM\UnitOfWork')->disableOriginalConstructor()->getMock();

        $args->expects($this->once())->method('getEntityManager')->willReturn($manager);
        $manager->expects($this->once())->method('getUnitOfWork')->willReturn($uow);
        $uow->expects($this->once())->method('getScheduledEntityInsertions')->willReturn([]);
        $uow->expects($this->once())->method('getScheduledEntityUpdates')->willReturn([]);
        $uow
            ->expects($this->once())
            ->method('getScheduledEntityDeletions')
            ->willReturn([new TaggableStub()])
        ;

        $subscriber = new TagSubscriber(\get_class($tag), true);
        $subscriber->onFlush($args);
    }

    public function testSetTags(): void
    {
        $tag = $this->getMockBuilder('Beelab\TagBundle\Tag\TagInterface')->getMock();
        /** @var OnFlushEventArgs&\PHPUnit\Framework\MockObject\MockObject $args */
        $args = $this->getMockBuilder(OnFlushEventArgs::class)->disableOriginalConstructor()->getMock();
        $manager = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $uow = $this->getMockBuilder('Doctrine\ORM\UnitOfWork')->disableOriginalConstructor()->getMock();

        $args->expects($this->once())->method('getEntityManager')->willReturn($manager);
        $manager->expects($this->once())->method('getUnitOfWork')->willReturn($uow);
        // TODO create some stubs of taggable entities and non-taggable entities...
        $uow->expects($this->once())->method('getScheduledEntityInsertions')->willReturn([$tag]);
        $uow->expects($this->once())->method('getScheduledEntityUpdates')->willReturn([]);
        $uow->expects($this->once())->method('getScheduledEntityDeletions')->willReturn([]);

        $subscriber = new TagSubscriber(\get_class($tag), true);
        $subscriber->onFlush($args);
    }
}
