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

namespace Zikula\ContentModule\ContentType;

use Zikula\ContentModule\ContentType\Form\Type\UnfilteredType as FormType;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\ExtensionsModule\ModuleInterface\Content\AbstractContentType;
use Zikula\ExtensionsModule\ModuleInterface\Content\ContentTypeInterface;

/**
 * Unfiltered raw content type.
 */
class UnfilteredType extends AbstractContentType
{
    /**
     * @var bool
     */
    protected $enableRawPlugin;

    public function getCategory(): string
    {
        return ContentTypeInterface::CATEGORY_EXPERT;
    }

    public function getIcon(): string
    {
        return 'user-secret';
    }

    public function getTitle(): string
    {
        return $this->translator->trans('Unfiltered raw text', [], 'contentTypes');
    }

    public function getDescription(): string
    {
        return $this->translator->trans('A plugin for unfiltered raw output (iframes, JavaScript, banners, etc).', [], 'contentTypes');
    }

    public function getAdminInfo(): string
    {
        return $this->translator->trans('You need to explicitly enable a checkbox in the configuration form to activate this plugin.', [], 'config');
    }

    public function isActive(): bool
    {
        // Only active when the admin has enabled this plugin
        return $this->enableRawPlugin && parent::isActive();
    }

    public function getDefaultData(): array
    {
        return [
            'text' => $this->translator->trans('Add unfiltered text here ...', [], 'contentTypes'),
            'useiframe' => false,
            'iframeSrc' => '',
            'iframeName' => '',
            'iframeTitle' => '',
            'iframeStyle' => 'border: 0',
            'iframeWidth' => 800,
            'iframeHeight' => 600,
            'iframeBorder' => 0,
            'iframeScrolling' => 'no',
            'iframeAllowTransparancy' => true
        ];
    }

    public function getTranslatableDataFields(): array
    {
        return ['text', 'iframeName', 'iframeTitle'];
    }

    public function getEditFormClass(): string
    {
        return FormType::class;
    }

    public function getAssets(string $context): array
    {
        $assets = parent::getAssets($context);
        if (ContentTypeInterface::CONTEXT_EDIT !== $context) {
            return $assets;
        }

        $assets['js'][] = $this->assetHelper->resolve(
            '@ZikulaContentModule:js/ZikulaContentModule.ContentType.Unfiltered.js'
        );

        return $assets;
    }

    public function getJsEntrypoint(string $context): ?string
    {
        if (ContentTypeInterface::CONTEXT_EDIT !== $context) {
            return null;
        }

        return 'contentInitUnfilteredEdit';
    }

    /**
     * @required
     */
    public function setEnableRawPlugin(VariableApiInterface $variableApi): void
    {
        $this->enableRawPlugin = (bool)$variableApi->get('ZikulaContentModule', 'enableRawPlugin');
    }
}
