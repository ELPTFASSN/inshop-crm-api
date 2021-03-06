<?php

namespace App\EventListener;

use App\Entity\Product;
use App\Service\Elastica\Client\ElasticaClientProduct;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Exception;

/**
 * Class ElasticaProductListener
 * @package App\EventListener
 */
class ElasticaProductListener
{
    /**
     * @var ElasticaClientProduct
     */
    protected ElasticaClientProduct $search;

    /**
     * @var bool
     */
    protected bool $enabled = true;

    /**
     * ElasticaProductListener constructor.
     * @param ElasticaClientProduct $search
     */
    public function __construct(ElasticaClientProduct $search)
    {
        $this->search = $search;
    }

    /**
     * Looks for new objects that should be indexed.
     *
     * @param LifecycleEventArgs $eventArgs
     * @throws Exception
     */
    public function postPersist(LifecycleEventArgs $eventArgs): void
    {
        $entity = $eventArgs->getObject();

        if ($this->enabled && $entity instanceof Product) {
            $this->search->addDocument($this->search->toArray($entity));
        }
    }

    /**
     * Looks for objects being updated that should be indexed or removed from the index.
     *
     * @param LifecycleEventArgs $eventArgs
     * @throws Exception
     */
    public function postUpdate(LifecycleEventArgs $eventArgs): void
    {
        /** @var Product $entity */
        $entity = $eventArgs->getObject();

        if ($this->enabled && $entity instanceof Product) {
            $this->search->addDocument($this->search->toArray($entity));
        }
    }

    /**
     * Delete objects preRemove instead of postRemove so that we have access to the id.  Because this is called
     * preRemove, first check that the entity is managed by Doctrine.
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function preRemove(LifecycleEventArgs $eventArgs): void
    {
        $entity = $eventArgs->getObject();

        if ($this->enabled && $entity instanceof Product) {
            $this->search->deleteDocument($entity->getId());
        }
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }
}
