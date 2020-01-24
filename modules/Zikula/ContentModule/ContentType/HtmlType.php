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

use Zikula\ContentModule\ContentType\Form\Type\HtmlType as FormType;
use Zikula\ExtensionsModule\ModuleInterface\Content\AbstractContentType;
use Zikula\ExtensionsModule\ModuleInterface\Content\ContentTypeInterface;

/**
 * HTML content type.
 */
class HtmlType extends AbstractContentType
{
    public function getCategory(): string
    {
        return ContentTypeInterface::CATEGORY_BASIC;
    }

    public function getIcon(): string
    {
        return 'font';
    }

    public function getTitle(): string
    {
        return $this->translator->trans('HTML text', [], 'contentTypes');
    }

    public function getDescription(): string
    {
        return $this->translator->trans('HTML editor for adding markup text to your page.', [], 'contentTypes');
    }

    public function getDefaultData(): array
    {
        return [
            'text' => $this->translator->trans('Add text here...', [], 'contentTypes')
        ];
    }

    public function getTranslatableDataFields(): array
    {
        return ['text'];
    }

    public function getSearchableText(): string
    {
        return html_entity_decode(strip_tags($this->data['text']));
    }

    public function getEditFormClass(): string
    {
        return FormType::class;
    }

    public function getAssets(string $context): array
    {
        $assets = parent::getAssets($context);
        if (in_array($context, [ContentTypeInterface::CONTEXT_EDIT, ContentTypeInterface::CONTEXT_TRANSLATION], true)) {
            $assets['js'][] = $this->assetHelper->resolve(
                '@ZikulaContentModule:js/ZikulaContentModule.ContentType.Html.js'
            );
        }

        return $assets;
    }

    public function getJsEntrypoint(string $context): ?string
    {
        if (ContentTypeInterface::CONTEXT_EDIT === $context) {
            return 'contentInitHtmlEdit';
        }
        if (ContentTypeInterface::CONTEXT_TRANSLATION === $context) {
            return 'contentInitHtmlTranslation';
        }

        return null;
    }
}
