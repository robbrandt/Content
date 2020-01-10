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

use Zikula\Common\Content\AbstractContentType;
use Zikula\Common\Content\ContentTypeInterface;
use Zikula\ContentModule\ContentType\Form\Type\QuoteType as FormType;

/**
 * Quote content type.
 */
class QuoteType extends AbstractContentType
{
    public function getCategory(): string
    {
        return ContentTypeInterface::CATEGORY_BASIC;
    }

    public function getIcon(): string
    {
        return 'quote-right';
    }

    public function getTitle(): string
    {
        return $this->translator->trans('Quote');
    }

    public function getDescription(): string
    {
        return $this->translator->trans('A highlighted quote with source.');
    }

    public function getDefaultData(): array
    {
        return [
            'text' => $this->translator->trans('Add quote text here...'),
            'source' => 'https://',
            'description' => $this->translator->trans('Name of the source')
        ];
    }

    public function getTranslatableDataFields(): array
    {
        return ['text', 'source', 'description'];
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
