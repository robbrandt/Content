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

namespace Zikula\ContentModule\Entity\Base;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Zikula\Core\Doctrine\EntityAccess;
use Zikula\ContentModule\Traits\StandardFieldsTrait;
use Zikula\ContentModule\Validator\Constraints as ContentAssert;

/**
 * Entity class that defines the entity structure and behaviours.
 *
 * This is the base entity class for searchable entities.
 * The following annotation marks it as a mapped superclass so subclasses
 * inherit orm properties.
 *
 * @ORM\MappedSuperclass
 */
abstract class AbstractSearchableEntity extends EntityAccess
{
    /**
     * Hook standard fields behaviour embedding createdBy, updatedBy, createdDate, updatedDate fields.
     */
    use StandardFieldsTrait;

    /**
     * @var string The tablename this object maps to
     */
    protected $_objectType = 'searchable';
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", unique=true)
     * @var integer $id
     */
    protected $id = 0;
    
    /**
     * the current workflow state
     *
     * @ORM\Column(length=20)
     * @Assert\NotBlank()
     * @ContentAssert\ListEntry(entityName="searchable", propertyName="workflowState", multiple=false)
     * @var string $workflowState
     */
    protected $workflowState = 'initial';
    
    /**
     * @ORM\Column(type="text", length=50000)
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="50000")
     * @var text $searchText
     */
    protected $searchText = '';
    
    /**
     * @ORM\Column(length=10)
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="10")
     * @Assert\Locale()
     * @var string $searchLanguage
     */
    protected $searchLanguage = '';
    
    
    /**
     * Bidirectional - Many searchables [searchables] are linked by one contentItem [content item] (OWNING SIDE).
     *
     * @ORM\ManyToOne(targetEntity="Zikula\ContentModule\Entity\ContentItemEntity", inversedBy="searchables")
     * @ORM\JoinTable(name="zikula_content_contentitem",
     *      joinColumns={@ORM\JoinColumn(name="id", referencedColumnName="id" , nullable=false)},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id", referencedColumnName="id" , nullable=false)}
     * )
     * @Assert\NotNull(message="Choosing a content item is required.")
     * @Assert\Type(type="Zikula\ContentModule\Entity\ContentItemEntity")
     * @var \Zikula\ContentModule\Entity\ContentItemEntity $contentItem
     */
    protected $contentItem;
    
    
    /**
     * SearchableEntity constructor.
     *
     * Will not be called by Doctrine and can therefore be used
     * for own implementation purposes. It is also possible to add
     * arbitrary arguments as with every other class method.
     */
    public function __construct()
    {
    }
    
    /**
     * Returns the _object type.
     *
     * @return string
     */
    public function get_objectType()
    {
        return $this->_objectType;
    }
    
    /**
     * Sets the _object type.
     *
     * @param string $_objectType
     *
     * @return void
     */
    public function set_objectType($_objectType)
    {
        if ($this->_objectType != $_objectType) {
            $this->_objectType = $_objectType;
        }
    }
    
    
    /**
     * Returns the id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Sets the id.
     *
     * @param integer $id
     *
     * @return void
     */
    public function setId($id)
    {
        if (intval($this->id) !== intval($id)) {
            $this->id = intval($id);
        }
    }
    
    /**
     * Returns the workflow state.
     *
     * @return string
     */
    public function getWorkflowState()
    {
        return $this->workflowState;
    }
    
    /**
     * Sets the workflow state.
     *
     * @param string $workflowState
     *
     * @return void
     */
    public function setWorkflowState($workflowState)
    {
        if ($this->workflowState !== $workflowState) {
            $this->workflowState = isset($workflowState) ? $workflowState : '';
        }
    }
    
    /**
     * Returns the search text.
     *
     * @return text
     */
    public function getSearchText()
    {
        return $this->searchText;
    }
    
    /**
     * Sets the search text.
     *
     * @param text $searchText
     *
     * @return void
     */
    public function setSearchText($searchText)
    {
        if ($this->searchText !== $searchText) {
            $this->searchText = isset($searchText) ? $searchText : '';
        }
    }
    
    /**
     * Returns the search language.
     *
     * @return string
     */
    public function getSearchLanguage()
    {
        return $this->searchLanguage;
    }
    
    /**
     * Sets the search language.
     *
     * @param string $searchLanguage
     *
     * @return void
     */
    public function setSearchLanguage($searchLanguage)
    {
        if ($this->searchLanguage !== $searchLanguage) {
            $this->searchLanguage = isset($searchLanguage) ? $searchLanguage : '';
        }
    }
    
    
    /**
     * Returns the content item.
     *
     * @return \Zikula\ContentModule\Entity\ContentItemEntity
     */
    public function getContentItem()
    {
        return $this->contentItem;
    }
    
    /**
     * Sets the content item.
     *
     * @param \Zikula\ContentModule\Entity\ContentItemEntity $contentItem
     *
     * @return void
     */
    public function setContentItem($contentItem = null)
    {
        $this->contentItem = $contentItem;
    }
    
    
    
    /**
     * Creates url arguments array for easy creation of display urls.
     *
     * @return array List of resulting arguments
     */
    public function createUrlArgs()
    {
        return [
            'id' => $this->getId()
        ];
    }
    
    /**
     * Returns the primary key.
     *
     * @return integer The identifier
     */
    public function getKey()
    {
        return $this->getId();
    }
    
    /**
     * Determines whether this entity supports hook subscribers or not.
     *
     * @return boolean
     */
    public function supportsHookSubscribers()
    {
        return true;
    }
    
    /**
     * Return lower case name of multiple items needed for hook areas.
     *
     * @return string
     */
    public function getHookAreaPrefix()
    {
        return 'zikulacontentmodule.ui_hooks.searchables';
    }
    
    /**
     * Returns an array of all related objects that need to be persisted after clone.
     * 
     * @param array $objects Objects that are added to this array
     * 
     * @return array List of entity objects
     */
    public function getRelatedObjectsToPersist(&$objects = [])
    {
        return [];
    }
    
    /**
     * ToString interceptor implementation.
     * This method is useful for debugging purposes.
     *
     * @return string The output string for this entity
     */
    public function __toString()
    {
        return 'Searchable ' . $this->getKey() . ': ' . $this->getSearchLanguage();
    }
    
    /**
     * Clone interceptor implementation.
     * This method is for example called by the reuse functionality.
     * Performs a quite simple shallow copy.
     *
     * See also:
     * (1) http://docs.doctrine-project.org/en/latest/cookbook/implementing-wakeup-or-clone.html
     * (2) http://www.php.net/manual/en/language.oop5.cloning.php
     * (3) http://stackoverflow.com/questions/185934/how-do-i-create-a-copy-of-an-object-in-php
     */
    public function __clone()
    {
        // if the entity has no identity do nothing, do NOT throw an exception
        if (!$this->id) {
            return;
        }
    
        // otherwise proceed
    
        // unset identifier
        $this->setId(0);
    
        // reset workflow
        $this->setWorkflowState('initial');
    
        $this->setCreatedBy(null);
        $this->setCreatedDate(null);
        $this->setUpdatedBy(null);
        $this->setUpdatedDate(null);
    
    }
}
