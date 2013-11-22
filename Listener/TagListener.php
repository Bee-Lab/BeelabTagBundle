<?php

namespace Beelab\TagBundle\Listener;

use Beelab\TagBundle\Tag\TagInterface;
use Beelab\TagBundle\Tag\TaggableInterface;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\ORM\Event\OnFlushEventArgs;

/**
 * Add tags to entities that implements TaggableInterface
 */
class TagListener
{
    protected $em, $uow, $tag;

    /**
     * Constructor
     *
     * @param string $tagClassName
     */
    public function __construct($tagClassName)
    {
        if (!class_exists($tagClassName)) {
            throw MappingException::nonExistingClass($tagClassName);
        }
        $this->tag = new $tagClassName();
        if (!$this->tag instanceof TagInterface) {
            throw new \RuntimeException(sprintf('Class "%s" must implment TagInterface.', $tagClassName));
        }
    }

    /**
     * Main method
     *
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $this->em = $args->getEntityManager();
        $this->uow = $this->em->getUnitOfWork();
        foreach ($this->uow->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof TaggableInterface) {
                $this->setTags($entity);
            }
        }
        foreach ($this->uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof TaggableInterface) {
                $this->setTags($entity, true);
            }
        }
        foreach ($this->uow->getScheduledEntityDeletions() as $entity) {
            // TODO remove possible tags that are unassociated?
        }
    }

    /**
     * Do the stuff
     *
     * @param TaggableInterface $entity
     * @param boolean           $update true if entity is being updated, false otherwise
     */
    protected function setTags(TaggableInterface $entity, $update = false)
    {
        $tagNames = $entity->getTagNames();
        if (empty($tagNames)) {
            return;
        }
        $tagClassMetadata = $this->em->getClassMetadata(get_class($this->tag));
        $repository = $this->em->getRepository(get_class($this->tag));
        foreach ($tagNames as $tagName) {
            $tag = $repository->findOneByName($tagName);
            if (empty($tag)) {
                // if tag doesn't exist, create it
                $tag = clone $this->tag;
                $tag->setName($tagName);
                $this->em->persist($tag);
                // see http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/events.html#onflush
                $this->uow->computeChangeSet($tagClassMetadata, $tag);
            }
            if (!$entity->hasTag($tag)) {
                // add tag only if not already added
                $entity->addTag($tag);
            }
        }
        // if updating, need to check if some tags were removed
        if ($update) {
            foreach ($entity->getTags() as $oldTag) {
                if (!in_array($oldTag->getName(), $tagNames)) {
                    $entity->removeTag($oldTag);
                }
            }
        }
        // see http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/events.html#onflush
        $entityClassMetadata = $this->em->getClassMetadata(get_class($entity));
        $this->uow->computeChangeSets($entityClassMetadata, $entity);
    }
}