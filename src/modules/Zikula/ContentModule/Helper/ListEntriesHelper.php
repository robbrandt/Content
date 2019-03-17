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

namespace Zikula\ContentModule\Helper;

use Zikula\Common\Translator\TranslatorInterface;
use Zikula\ContentModule\Helper\Base\AbstractListEntriesHelper;
use Zikula\GroupsModule\Entity\RepositoryInterface\GroupRepositoryInterface;

/**
 * Helper implementation class for list field entries related methods.
 */
class ListEntriesHelper extends AbstractListEntriesHelper
{
    /**
     * @var GroupRepositoryInterface
     */
    protected $groupRepository;

    /**
     * ListEntriesHelper constructor.
     *
     * @param TranslatorInterface $translator
     * @param GroupRepositoryInterface $groupRepository
     */
    public function __construct(TranslatorInterface $translator, GroupRepositoryInterface $groupRepository)
    {
        parent::__construct($translator);
        $this->groupRepository = $groupRepository;
    }

    /**
     * @inheritDoc
     */
    public function getScopeEntriesForPage()
    {
        $states = parent::getScopeEntriesForPage();

        $states = $this->addUserGroupEntries($states);

        return $states;
    }

    /**
     * @inheritDoc
     */
    public function getScopeEntriesForContentItem()
    {
        $states = parent::getScopeEntriesForContentItem();

        $states = $this->addUserGroupEntries($states);

        return $states;
    }

    /**
     * Adds a list of user groups to the given array.
     *
     * @param array $states
     *
     * @return array
     */
    private function addUserGroupEntries($states)
    {
        $groups = $this->groupRepository->findAll();
        foreach ($groups as $group) {
            $states[] = [
                'value'   => $group->getGid(),
                'text'    => $this->__f('Group %group', ['%group' => $group->getName()]),
                'title'   => '',
                'image'   => '',
                'default' => false
            ];
        }

        return $states;
    }
}
