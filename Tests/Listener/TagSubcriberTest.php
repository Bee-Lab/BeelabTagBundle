<?php

namespace Beelab\TagBundle\Tests\Listener;

use Beelab\TagBundle\Listener\TagSubscriber;
use Beelab\TagBundle\Test\NonTaggableStub;
use Beelab\TagBundle\Test\TagStub;
use Beelab\TagBundle\Test\TaggableStub;
use Beelab\TagBundle\Test\TaggableStub2;
use Beelab\TagBundle\Test\TaggableStub3;

/**
 * @group unit
 */
class TagSubcriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException Doctrine\Common\Persistence\Mapping\MappingException
     */
    public function testNonexistentClass()
    {
        $subscriber = new TagSubscriber('ClassDoesNotExist');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidClass()
    {
        $subscriber = new TagSubscriber('Beelab\TagBundle\Test\NonTaggableStub');
    }

    public function testGetSubscribedEvents()
    {
        $tag = $this->getMock('Beelab\TagBundle\Tag\TagInterface');
        $subscriber = new TagSubscriber(get_class($tag));

        $this->assertContains('onFlush', $subscriber->getSubscribedEvents());
    }

    public function testOnFlush()
    {
        $tag = $this->getMock('Beelab\TagBundle\Tag\TagInterface');
        $args = $this->getMockBuilder('Doctrine\ORM\Event\OnFlushEventArgs')->disableOriginalConstructor()->getMock();
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')->disableOriginalConstructor()->getMock();
        $uow = $this->getMockBuilder('Doctrine\ORM\UnitOfWork')->disableOriginalConstructor()->getMock();
        $metadata = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')->disableOriginalConstructor()->getMock();

        $args->expects($this->once())->method('getEntityManager')->will($this->returnValue($em));
        $em->expects($this->once())->method('getUnitOfWork')->will($this->returnValue($uow));
        $em->expects($this->once())->method('getRepository')->will($this->returnValue($repo));
        $em->expects($this->any())->method('getClassMetadata')->will($this->returnValue($metadata));
        $uow
            ->expects($this->once())
            ->method('getScheduledEntityInsertions')
            ->will($this->returnValue(array(new TaggableStub(), new NonTaggableStub())))
        ;
        $uow
            ->expects($this->once())
            ->method('getScheduledEntityUpdates')
            ->will($this->returnValue(array(new TaggableStub2())))
        ;
        $uow->expects($this->never())->method('getScheduledEntityDeletions');

        $subscriber = new TagSubscriber(get_class($tag));
        $subscriber->onFlush($args);
    }

    public function testOnFlushEntityWithoutTags()
    {
        $tag = $this->getMock('Beelab\TagBundle\Tag\TagInterface');
        $args = $this->getMockBuilder('Doctrine\ORM\Event\OnFlushEventArgs')->disableOriginalConstructor()->getMock();
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $uow = $this->getMockBuilder('Doctrine\ORM\UnitOfWork')->disableOriginalConstructor()->getMock();
        $metadata = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')->disableOriginalConstructor()->getMock();

        $args->expects($this->once())->method('getEntityManager')->will($this->returnValue($em));
        $em->expects($this->once())->method('getUnitOfWork')->will($this->returnValue($uow));
        $em->expects($this->any())->method('getClassMetadata')->will($this->returnValue($metadata));
        $uow
            ->expects($this->once())
            ->method('getScheduledEntityInsertions')
            ->will($this->returnValue(array(new TaggableStub3())))
        ;
        $uow
            ->expects($this->once())
            ->method('getScheduledEntityUpdates')
            ->will($this->returnValue(array()))
        ;
        $uow->expects($this->never())->method('getScheduledEntityDeletions');

        $subscriber = new TagSubscriber(get_class($tag));
        $subscriber->onFlush($args);
    }

    public function testOnFlushWithPurge()
    {
        $tag = new \Beelab\TagBundle\Test\TagStub;
        $args = $this->getMockBuilder('Doctrine\ORM\Event\OnFlushEventArgs')->disableOriginalConstructor()->getMock();
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $uow = $this->getMockBuilder('Doctrine\ORM\UnitOfWork')->disableOriginalConstructor()->getMock();

        $args->expects($this->once())->method('getEntityManager')->will($this->returnValue($em));
        $em->expects($this->once())->method('getUnitOfWork')->will($this->returnValue($uow));
        $uow->expects($this->once())->method('getScheduledEntityInsertions')->will($this->returnValue(array()));
        $uow->expects($this->once())->method('getScheduledEntityUpdates')->will($this->returnValue(array()));
        $uow
            ->expects($this->once())
            ->method('getScheduledEntityDeletions')
            ->will($this->returnValue(array(new TaggableStub())))
        ;

        $subscriber = new TagSubscriber(get_class($tag), true);
        $subscriber->onFlush($args);
    }

    public function testSetTags()
    {
        $tag = $this->getMock('Beelab\TagBundle\Tag\TagInterface');
        $args = $this->getMockBuilder('Doctrine\ORM\Event\OnFlushEventArgs')->disableOriginalConstructor()->getMock();
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $uow = $this->getMockBuilder('Doctrine\ORM\UnitOfWork')->disableOriginalConstructor()->getMock();

        $args->expects($this->once())->method('getEntityManager')->will($this->returnValue($em));
        $em->expects($this->once())->method('getUnitOfWork')->will($this->returnValue($uow));
        // TODO create some stubs of taggable entities and non-taggable entities...
        $uow->expects($this->once())->method('getScheduledEntityInsertions')->will($this->returnValue(array($tag)));
        $uow->expects($this->once())->method('getScheduledEntityUpdates')->will($this->returnValue(array()));
        $uow->expects($this->once())->method('getScheduledEntityDeletions')->will($this->returnValue(array()));

        $subscriber = new TagSubscriber(get_class($tag), true);
        $subscriber->onFlush($args);
    }
}
