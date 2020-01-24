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

namespace Zikula\ContentModule\ContentType;

use RuntimeException;
use Zikula\ContentModule\ContentType\Form\Type\ContentItemType as FormType;
use Zikula\ContentModule\Entity\Factory\EntityFactory;
use Zikula\ContentModule\Helper\ContentDisplayHelper;
use Zikula\ExtensionsModule\ModuleInterface\Content\AbstractContentType;
use Zikula\ExtensionsModule\ModuleInterface\Content\ContentTypeInterface;

/**
 * Content item content type.
 */
class ContentItemType extends AbstractContentType
{
    /**
     * @var EntityFactory
     */
    protected $entityFactory;

    /**
     * @var ContentDisplayHelper
     */
    protected $displayHelper;

    public function getCategory(): string
    {
        return ContentTypeInterface::CATEGORY_EXPERT;
    }

    public function getIcon(): string
    {
        return 'link';
    }

    public function getTitle(): string
    {
        return $this->translator->trans('Existing content', [], 'contentTypes');
    }

    public function getDescription(): string
    {
        return $this->translator->trans('Reference and display an already existing content item.', [], 'contentTypes');
    }

    public function getDefaultData(): array
    {
        return [
            'contentItemId' => 0
        ];
    }

    public function displayView(): string
    {
        if (1 > $this->data['contentItemId']) {
            return '';
        }

        $repository = $this->entityFactory->getRepository('contentItem');
        $contentItem = $repository->selectById($this->data['contentItemId']);
        if (null === $contentItem) {
            return '';
        }

        try {
            $contentType = $this->displayHelper->initContentType($contentItem);
        } catch (RuntimeException $exception) {
            return '';
        }

        return $contentType->displayView();
    }

    public function getEditFormClass(): string
    {
        return FormType::class;
    }

    /**
     * @required
     */
    public function setAdditionalDepencies(
        EntityFactory $entityFactory,
        ContentDisplayHelper $displayHelper
    ): void {
        $this->entityFactory = $entityFactory;
        $this->displayHelper = $displayHelper;
    }
}
