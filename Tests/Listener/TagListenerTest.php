<?php

namespace Beelab\TagBundle\Tests\Listner;

use Beelab\TagBundle\Listener\TagListener;

/**
 * @group unit
 */
class LastLoginListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException Doctrine\Common\Persistence\Mapping\MappingException
     */
    public function testNonexistentClass()
    {
        $listener = new TagListener('ClassDoesNotExist');
    }

    /**
     * @expectedException RuntimeException
     */
    public function testInvalidClass()
    {
        $listener = new TagListener('Beelab\TagBundle\Tests\Listner\LastLoginListenerTest');
    }

    public function testOnFlush()
    {
        $tag = $this->getMock('Beelab\TagBundle\Tag\TagInterface');
        $args = $this->getMockBuilder('Doctrine\ORM\Event\OnFlushEventArgs')->disableOriginalConstructor()->getMock();
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $uow = $this->getMockBuilder('Doctrine\ORM\UnitOfWork')->disableOriginalConstructor()->getMock();

        $args->expects($this->once())->method('getEntityManager')->will($this->returnValue($em));
        $em->expects($this->once())->method('getUnitOfWork')->will($this->returnValue($uow));
        $uow->expects($this->once())->method('getScheduledEntityInsertions')->will($this->returnValue(array()));
        $uow->expects($this->once())->method('getScheduledEntityUpdates')->will($this->returnValue(array()));
        $uow->expects($this->once())->method('getScheduledEntityDeletions')->will($this->returnValue(array()));

        $listener = new TagListener(get_class($tag));
        $listener->onFlush($args);
    }

    public function testSetTags()
    {
        $this->markTestIncomplete('TODO');
    }
}