<?php
/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <vorstand@zikula.de>.
 * @link https://zikula.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 1.3.2 (https://modulestudio.de).
 */

namespace Zikula\ContentModule\HookSubscriber\Base;

use Zikula\Bundle\HookBundle\Category\FilterHooksCategory;
use Zikula\Bundle\HookBundle\HookSubscriberInterface;
use Zikula\Common\Translator\TranslatorInterface;

/**
 * Base class for filter hooks subscriber.
 */
abstract class AbstractSearchableFilterHooksSubscriber implements HookSubscriberInterface
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * SearchableFilterHooksSubscriber constructor.
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @inheritDoc
     */
    public function getOwner()
    {
        return 'ZikulaContentModule';
    }
    
    /**
     * @inheritDoc
     */
    public function getCategory()
    {
        return FilterHooksCategory::NAME;
    }
    
    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->translator->__('Searchable filter hooks subscriber');
    }

    /**
     * @inheritDoc
     */
    public function getEvents()
    {
        return [
            FilterHooksCategory::TYPE_FILTER => 'zikulacontentmodule.filter_hooks.searchables.filter'
        ];
    }
}