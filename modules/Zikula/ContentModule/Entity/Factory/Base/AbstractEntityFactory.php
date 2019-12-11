<?php

/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 * @see https://ziku.la
 * @version Generated by ModuleStudio 1.4.0 (https://modulestudio.de).
 */

declare(strict_types=1);

namespace Zikula\ContentModule\Entity\Factory\Base;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use InvalidArgumentException;
use Zikula\ContentModule\Entity\Factory\EntityInitialiser;
use Zikula\ContentModule\Entity\PageEntity;
use Zikula\ContentModule\Entity\ContentItemEntity;
use Zikula\ContentModule\Helper\CollectionFilterHelper;
use Zikula\ContentModule\Helper\FeatureActivationHelper;

/**
 * Factory class used to create entities and receive entity repositories.
 */
abstract class AbstractEntityFactory
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var EntityInitialiser
     */
    protected $entityInitialiser;

    /**
     * @var CollectionFilterHelper
     */
    protected $collectionFilterHelper;

    /**
     * @var FeatureActivationHelper
     */
    protected $featureActivationHelper;

    public function __construct(
        EntityManagerInterface $entityManager,
        EntityInitialiser $entityInitialiser,
        CollectionFilterHelper $collectionFilterHelper,
        FeatureActivationHelper $featureActivationHelper)
    {
        $this->entityManager = $entityManager;
        $this->entityInitialiser = $entityInitialiser;
        $this->collectionFilterHelper = $collectionFilterHelper;
        $this->featureActivationHelper = $featureActivationHelper;
    }

    /**
     * Returns a repository for a given object type.
     */
    public function getRepository(string $objectType): EntityRepository
    {
        $entityClass = 'Zikula\\ContentModule\\Entity\\' . ucfirst($objectType) . 'Entity';

        /** @var EntityRepository $repository */
        $repository = $this->getEntityManager()->getRepository($entityClass);
        $repository->setCollectionFilterHelper($this->collectionFilterHelper);

        if (in_array($objectType, ['page', 'contentItem'], true)) {
            $repository->setTranslationsEnabled($this->featureActivationHelper->isEnabled(FeatureActivationHelper::TRANSLATIONS, $objectType));
        }

        return $repository;
    }

    /**
     * Creates a new page instance.
     */
    public function createPage(): PageEntity
    {
        $entity = new PageEntity();

        $this->entityInitialiser->initPage($entity);

        return $entity;
    }

    /**
     * Creates a new contentItem instance.
     */
    public function createContentItem(): ContentItemEntity
    {
        $entity = new ContentItemEntity();

        $this->entityInitialiser->initContentItem($entity);

        return $entity;
    }

    /**
     * Returns the identifier field's name for a given object type.
     */
    public function getIdField(string $objectType = ''): string
    {
        if (empty($objectType)) {
            throw new InvalidArgumentException('Invalid object type received.');
        }
        $entityClass = 'ZikulaContentModule:' . ucfirst($objectType) . 'Entity';
    
        $meta = $this->getEntityManager()->getClassMetadata($entityClass);
    
        return $meta->getSingleIdentifierFieldName();
    }

    public function getEntityManager(): ?EntityManagerInterface
    {
        return $this->entityManager;
    }
    
    public function setEntityManager(EntityManagerInterface $entityManager = null): void
    {
        if ($this->entityManager !== $entityManager) {
            $this->entityManager = $entityManager;
        }
    }
    
    public function getEntityInitialiser(): ?EntityInitialiser
    {
        return $this->entityInitialiser;
    }
    
    public function setEntityInitialiser(EntityInitialiser $entityInitialiser = null): void
    {
        if ($this->entityInitialiser !== $entityInitialiser) {
            $this->entityInitialiser = $entityInitialiser;
        }
    }
    
}
