<?php

/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 * @see https://ziku.la
 * @version Generated by ModuleStudio 1.5.0 (https://modulestudio.de).
 */

declare(strict_types=1);

namespace Zikula\ContentModule\Helper;

use Zikula\Bundle\CoreBundle\Doctrine\EntityAccess;
use Zikula\ContentModule\Entity\Repository\ContentItemTranslationRepository;
use Zikula\ContentModule\Helper\Base\AbstractLoggableHelper;
use Zikula\ContentModule\Entity\PageEntity;
use Zikula\ContentModule\Entity\ContentItemTranslationEntity;

/**
 * Helper implementation class for loggable behaviour.
 */
class LoggableHelper extends AbstractLoggableHelper
{
    protected function translateActionDescriptionInternal($text = '', array $parameters = []): string
    {
        $actionTranslated = parent::translateActionDescriptionInternal($text, $parameters);
        switch ($text) {
            case '_HISTORY_PAGE_CONTENT_CREATED':
                $actionTranslated = $this->trans('Content created', [], 'page');
                break;
            case '_HISTORY_PAGE_CONTENT_UPDATED':
                $actionTranslated = $this->trans('Content updated', [], 'page');
                break;
            case '_HISTORY_PAGE_CONTENT_CLONED':
                $actionTranslated = $this->trans('Content cloned', [], 'page');
                break;
            case '_HISTORY_PAGE_CONTENT_DELETED':
                $actionTranslated = $this->trans('Content deleted', [], 'page');
                break;
            case '_HISTORY_PAGE_LAYOUT_CHANGED':
                $actionTranslated = $this->trans('Layout changed (e.g. content moved or resized)', [], 'page');
                break;
        }

        return $actionTranslated;
    }

    /**
     * Stores data about a page's content items and their translations into the contentData
     * field of the owning page in order to add this information into the revisioning.
     */
    public function updateContentData(PageEntity $page): void
    {
        $contentData = [];
        $entityManager = $this->entityFactory->getEntityManager();
        /** @var ContentItemTranslationRepository $translationRepository */
        $translationRepository = $entityManager->getRepository(ContentItemTranslationEntity::class);
        $supportedLanguages = $this->translatableHelper->getSupportedLanguages('contentItem');
        $fields = $this->translatableHelper->getTranslatableFields('contentItem');

        $contentIds = [];
        foreach ($page->getContentItems() as $item) {
            if (in_array($item->getId(), $contentIds, true)) {
                continue;
            }
            $contentIds[] = $item->getId();
            $itemData = [
                'id' => $item->getId(),
                'workflowState' => $item->getWorkflowState(),
                'owningType' => $item->getOwningType(),
                'contentData' => $item->getContentData(),
                'active' => $item->getActive(),
                'activeFrom' => $item->getActiveFrom(),
                'activeTo' => $item->getActiveTo(),
                'scope' => $item->getScope(),
                'stylingClasses' => $item->getStylingClasses(),
                'searchText' => $item->getSearchText(),
                'additionalSearchText' => $item->getAdditionalSearchText(),
                'translations' => []
            ];

            // collect translations
            $entityTranslations = $translationRepository->findTranslations($item);
            foreach ($supportedLanguages as $language) {
                $translationData = [];
                foreach ($fields as $fieldName) {
                    $translationData[$fieldName] = $entityTranslations[$language][$fieldName] ?? '';
                }
                // add data to collected translations
                $itemData['translations'][$language] = $translationData;
            }

            $contentData[] = $itemData;
        }

        $page->setContentData($contentData);
    }

    public function revert(EntityAccess $entity, int $requestedVersion = 1, bool $detach = false): EntityAccess
    {
        // revert content items
        $entityManager = $this->entityFactory->getEntityManager();
        if ($entity instanceof PageEntity) {
            foreach ($entity->getContentItems() as $item) {
                $entity->removeContentItems($item);
                if (true === $detach) {
                    $entityManager->detach($item);
                } else {
                    $entityManager->remove($item);
                }
            }
            if (true !== $detach) {
                $entityManager->flush();
                $this->translatableHelper->cleanupTranslationsForContentItems();
            }
        }

        $entity = parent::revert($entity, $requestedVersion, $detach);
        if (!($entity instanceof PageEntity)) {
            return $entity;
        }

        $currentLanguage = $this->translatableHelper->getCurrentLanguage();
        $contentData = $entity->getContentData();
        $contentIds = [];
        foreach ($contentData as $itemData) {
            if (in_array($itemData['id'], $contentIds, true)) {
                continue;
            }
            $contentIds[] = $itemData['id'];

            $translations = $itemData['translations'];
            unset($itemData['translations']);

            $newItem = $this->entityFactory->createContentItem();
            $newItem->merge($itemData);

            $entity->addContentItems($newItem);
            if (true === $detach) {
                $entityManager->detach($newItem);

                if (isset($translations[$currentLanguage])) {
                    foreach ($translations[$currentLanguage] as $fieldName => $fieldData) {
                        if ('contentData' === $fieldName) {
                            $fieldData = @unserialize($fieldData);
                        }
                        $setter = 'set' . ucfirst($fieldName);
                        $newItem->$setter($fieldData);
                    }
                    $newItem->setLocale($currentLanguage);
                }
            } else {
                $entityManager->persist($newItem);
                $entityManager->flush();
                foreach ($translations as $language => $translationData) {
                    foreach ($translationData as $fieldName => $fieldData) {
                        if ('contentData' === $fieldName) {
                            $fieldData = @unserialize($fieldData);
                        }
                        $setter = 'set' . ucfirst($fieldName);
                        $newItem->$setter($fieldData);
                    }
                    $newItem->setLocale($language);
                    $entityManager->flush();
                }
            }
        }

        if (true !== $detach) {
            $entityManager->flush();
        }

        return $entity;
    }
}
