<?php
/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 * @link https://ziku.la
 * @version Generated by ModuleStudio 1.3.2 (https://modulestudio.de).
 */

namespace Zikula\ContentModule\Helper\Base;

use IntlDateFormatter;
use Symfony\Component\HttpFoundation\RequestStack;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\ContentModule\Entity\PageEntity;
use Zikula\ContentModule\Entity\ContentItemEntity;
use Zikula\ContentModule\Entity\SearchableEntity;
use Zikula\ContentModule\Helper\ListEntriesHelper;

/**
 * Entity display helper base class.
 */
abstract class AbstractEntityDisplayHelper
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var ListEntriesHelper Helper service for managing list entries
     */
    protected $listEntriesHelper;

    /**
     * @var IntlDateFormatter Formatter for dates
     */
    protected $dateFormatter;

    /**
     * EntityDisplayHelper constructor.
     *
     * @param TranslatorInterface $translator        Translator service instance
     * @param RequestStack        $requestStack      RequestStack service instance
     * @param ListEntriesHelper   $listEntriesHelper Helper service for managing list entries
     */
    public function __construct(
        TranslatorInterface $translator,
        RequestStack $requestStack,
        ListEntriesHelper $listEntriesHelper
    ) {
        $this->translator = $translator;
        $this->listEntriesHelper = $listEntriesHelper;
        $locale = null !== $requestStack->getCurrentRequest() ? $requestStack->getCurrentRequest()->getLocale() : null;
        $this->dateFormatter = new IntlDateFormatter($locale, IntlDateFormatter::NONE, IntlDateFormatter::NONE);
    }

    /**
     * Returns the formatted title for a given entity.
     *
     * @param object $entity The given entity instance
     *
     * @return string The formatted title
     */
    public function getFormattedTitle($entity)
    {
        if ($entity instanceof PageEntity) {
            return $this->formatPage($entity);
        }
        if ($entity instanceof ContentItemEntity) {
            return $this->formatContentItem($entity);
        }
        if ($entity instanceof SearchableEntity) {
            return $this->formatSearchable($entity);
        }
    
        return '';
    }
    
    /**
     * Returns the formatted title for a given entity.
     *
     * @param PageEntity $entity The given entity instance
     *
     * @return string The formatted title
     */
    protected function formatPage(PageEntity $entity)
    {
        return $this->translator->__f('%title%', [
            '%title%' => $entity->getTitle()
        ]);
    }
    
    /**
     * Returns the formatted title for a given entity.
     *
     * @param ContentItemEntity $entity The given entity instance
     *
     * @return string The formatted title
     */
    protected function formatContentItem(ContentItemEntity $entity)
    {
        return $this->translator->__f('%owningType%', [
            '%owningType%' => $entity->getOwningType()
        ]);
    }
    
    /**
     * Returns the formatted title for a given entity.
     *
     * @param SearchableEntity $entity The given entity instance
     *
     * @return string The formatted title
     */
    protected function formatSearchable(SearchableEntity $entity)
    {
        return $this->translator->__f('%searchLanguage%', [
            '%searchLanguage%' => $entity->getSearchLanguage()
        ]);
    }
    
    /**
     * Returns name of the field used as title / name for entities of this repository.
     *
     * @param string $objectType Name of treated entity type
     *
     * @return string Name of field to be used as title
     */
    public function getTitleFieldName($objectType)
    {
        if ($objectType == 'page') {
            return 'title';
        }
        if ($objectType == 'contentItem') {
            return 'owningType';
        }
        if ($objectType == 'searchable') {
            return '';
        }
    
        return '';
    }
    
    /**
     * Returns name of the field used for describing entities of this repository.
     *
     * @param string $objectType Name of treated entity type
     *
     * @return string Name of field to be used as description
     */
    public function getDescriptionFieldName($objectType)
    {
        if ($objectType == 'page') {
            return 'optionalText';
        }
        if ($objectType == 'contentItem') {
            return 'owningType';
        }
        if ($objectType == 'searchable') {
            return 'searchText';
        }
    
        return '';
    }
    
    /**
     * Returns name of the date(time) field to be used for representing the start
     * of this object. Used for providing meta data to the tag module.
     *
     * @param string $objectType Name of treated entity type
     *
     * @return string Name of field to be used as date
     */
    public function getStartDateFieldName($objectType)
    {
        if ($objectType == 'page') {
            return 'activeFrom';
        }
        if ($objectType == 'contentItem') {
            return 'activeFrom';
        }
        if ($objectType == 'searchable') {
            return 'createdDate';
        }
    
        return '';
    }
}
