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

use Zikula\ContentModule\ContentType\Form\Type\HeadingType as FormType;
use Zikula\ExtensionsModule\ModuleInterface\Content\AbstractContentType;
use Zikula\ExtensionsModule\ModuleInterface\Content\ContentTypeInterface;

/**
 * Heading content type.
 */
class HeadingType extends AbstractContentType
{
    public function getCategory(): string
    {
        return ContentTypeInterface::CATEGORY_BASIC;
    }

    public function getIcon(): string
    {
        return 'heading';
    }

    public function getTitle(): string
    {
        return $this->translator->trans('Heading', [], 'contentTypes');
    }

    public function getDescription(): string
    {
        return $this->translator->trans('Section heading (or page title) for structuring large amounts of text.', [], 'contentTypes');
    }

    public function getDefaultData(): array
    {
        return [
            'text' => $this->translator->trans('Heading', [], 'contentTypes'),
            'headingType' => 'h3',
            'anchorName' => '',
            'displayPageTitle' => false
        ];
    }

    public function getTranslatableDataFields(): array
    {
        return ['text', 'anchorName'];
    }

    public function getData(): array
    {
        $data = parent::getData();

        if (true === $data['displayPageTitle']) {
            $data['text'] = $this->getEntity()->getPage()->getTitle();
        }

        return $data;
    }

    public function getSearchableText(): string
    {
        return html_entity_decode(strip_tags($this->data['text']));
    }

    public function getEditFormClass(): string
    {
        return FormType::class;
    }
}
