<?php

/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 *
 * @see https://ziku.la
 *
 * @version Generated by ModuleStudio 1.5.0 (https://modulestudio.de).
 */

declare(strict_types=1);

namespace Zikula\ContentModule\Helper\Base;

use IntlDateFormatter;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zikula\Bundle\CoreBundle\Doctrine\EntityAccess;
use Zikula\ContentModule\Entity\PageEntity;
use Zikula\ContentModule\Entity\ContentItemEntity;
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
     */
    public function getFormattedTitle(EntityAccess $entity): string
    {
        if ($entity instanceof PageEntity) {
            return $this->formatPage($entity);
        }
        if ($entity instanceof ContentItemEntity) {
            return $this->formatContentItem($entity);
        }
    
        return '';
    }
    
    /**
     * Returns the formatted title for a given entity.
     */
    protected function formatPage(PageEntity $entity): string
    {
        return $this->translator->trans(
            '%title%',
            [
                '%title%' => htmlspecialchars($entity->getTitle()),
            ],
            'page'
        );
    }
    
    /**
     * Returns the formatted title for a given entity.
     */
    protected function formatContentItem(ContentItemEntity $entity): string
    {
        return $this->translator->trans(
            '%owningType%',
            [
                '%owningType%' => htmlspecialchars($entity->getOwningType()),
            ],
            'contentItem'
        );
    }
    
    /**
     * Returns name of the field used as title / name for entities of this repository.
     */
    public function getTitleFieldName(string $objectType = ''): string
    {
        if ('page' === $objectType) {
            return 'title';
        }
        if ('contentItem' === $objectType) {
            return 'owningType';
        }
    
        return '';
    }
    
    /**
     * Returns name of the field used for describing entities of this repository.
     */
    public function getDescriptionFieldName(string $objectType = ''): string
    {
        if ('page' === $objectType) {
            return 'optionalText';
        }
        if ('contentItem' === $objectType) {
            return 'searchText';
        }
    
        return '';
    }
    
    /**
     * Returns name of the date(time) field to be used for representing the start
     * of this object. Used for providing meta data to the tag module.
     */
    public function getStartDateFieldName(string $objectType = ''): string
    {
        if ('page' === $objectType) {
            return 'activeFrom';
        }
        if ('contentItem' === $objectType) {
            return 'activeFrom';
        }
    
        return '';
    }
}
