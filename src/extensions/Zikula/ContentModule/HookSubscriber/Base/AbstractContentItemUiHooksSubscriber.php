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

namespace Zikula\ContentModule\HookSubscriber\Base;

use Symfony\Contracts\Translation\TranslatorInterface;
use Zikula\Bundle\HookBundle\Category\UiHooksCategory;
use Zikula\Bundle\HookBundle\HookSubscriberInterface;

/**
 * Base class for ui hooks subscriber.
 */
abstract class AbstractContentItemUiHooksSubscriber implements HookSubscriberInterface
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getOwner(): string
    {
        return 'ZikulaContentModule';
    }
    
    public function getCategory(): string
    {
        return UiHooksCategory::NAME;
    }
    
    public function getTitle(): string
    {
        return $this->translator->trans('Content item ui hooks subscriber', [], 'hooks');
    }

    public function getAreaName(): string
    {
        return 'subscriber.zikulacontentmodule.ui_hooks.contentitems';
    }

    public function getEvents(): array
    {
        return [
            // Display hook for create/edit forms.
            UiHooksCategory::TYPE_FORM_EDIT => 'zikulacontentmodule.ui_hooks.contentitems.form_edit',
            // Validate input from an item to be edited.
            UiHooksCategory::TYPE_VALIDATE_EDIT => 'zikulacontentmodule.ui_hooks.contentitems.validate_edit',
            // Perform the final update actions for an edited item.
            UiHooksCategory::TYPE_PROCESS_EDIT => 'zikulacontentmodule.ui_hooks.contentitems.process_edit',
            // Validate input from an item to be deleted.
            UiHooksCategory::TYPE_VALIDATE_DELETE => 'zikulacontentmodule.ui_hooks.contentitems.validate_delete',
            // Perform the final delete actions for a deleted item.
            UiHooksCategory::TYPE_PROCESS_DELETE => 'zikulacontentmodule.ui_hooks.contentitems.process_delete',
        ];
    }
}
