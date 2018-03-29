<?php
/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <vorstand@zikula.de>.
 * @link https://zikula.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 1.3.1 (https://modulestudio.de).
 */

namespace Zikula\ContentModule\Entity\Base;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Zikula\Core\Doctrine\EntityAccess;
use Zikula\ContentModule\Traits\LoggableStandardFieldsTrait;
use Zikula\ContentModule\Validator\Constraints as ContentAssert;

/**
 * Entity class that defines the entity structure and behaviours.
 *
 * This is the base entity class for page entities.
 * The following annotation marks it as a mapped superclass so subclasses
 * inherit orm properties.
 *
 * @ORM\MappedSuperclass
 */
abstract class AbstractPageEntity extends EntityAccess implements Translatable
{
    /**
     * Hook standard fields behaviour embedding createdBy, updatedBy, createdDate, updatedDate fields.
     */
    use LoggableStandardFieldsTrait;

    /**
     * @var string The tablename this object maps to
     */
    protected $_objectType = 'page';
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Gedmo\Versioned
     * @ORM\Column(type="integer", unique=true)
     * @var integer $id
     */
    protected $id = 0;
    
    /**
     * the current workflow state
     *
     * @Gedmo\Versioned
     * @ORM\Column(length=20)
     * @Assert\NotBlank()
     * @ContentAssert\ListEntry(entityName="page", propertyName="workflowState", multiple=false)
     * @var string $workflowState
     */
    protected $workflowState = 'initial';
    
    /**
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="255")
     * @var string $title
     */
    protected $title = '';
    
    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $showTitle
     */
    protected $showTitle = true;
    
    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="text", length=2000)
     * @Assert\NotNull()
     * @Assert\Length(min="0", max="2000")
     * @var text $metaDescription
     */
    protected $metaDescription = '';
    
    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $skipUiHookSubscriber
     */
    protected $skipUiHookSubscriber = false;
    
    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $skipFilterHookSubscriber
     */
    protected $skipFilterHookSubscriber = false;
    
    /**
     * @Gedmo\Versioned
     * @ORM\Column(length=100)
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="100")
     * @var string $layout
     */
    protected $layout = '';
    
    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="integer")
     * @Assert\Type(type="integer")
     * @Assert\NotNull()
     * @Assert\LessThan(value=100000000000)
     * @var integer $views
     */
    protected $views = 0;
    
    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $active
     */
    protected $active = true;
    
    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="datetime")
     * @Assert\NotNull()
     * @Assert\DateTime()
     * @var DateTime $activeFrom
     */
    protected $activeFrom;
    
    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="datetime")
     * @Assert\NotNull()
     * @Assert\DateTime()
     * @Assert\Expression("!value or value > this.getActiveFrom()")
     * @var DateTime $activeTo
     */
    protected $activeTo;
    
    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $inMenu
     */
    protected $inMenu = true;
    
    /**
     * @Gedmo\Versioned
     * @ORM\Column(length=10)
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="10")
     * @Assert\Locale()
     * @var string $pageLanguage
     */
    protected $pageLanguage = '';
    
    /**
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(length=255)
     * @Assert\NotNull()
     * @Assert\Length(min="0", max="255")
     * @var string $optionalString1
     */
    protected $optionalString1 = '';
    
    /**
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(length=255)
     * @Assert\NotNull()
     * @Assert\Length(min="0", max="255")
     * @var string $optionalString2
     */
    protected $optionalString2 = '';
    
    /**
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(type="text", length=2000)
     * @Assert\NotNull()
     * @Assert\Length(min="0", max="2000")
     * @var text $optionalText
     */
    protected $optionalText = '';
    
    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="integer")
     * @ORM\Version
     * @Assert\Type(type="integer")
     * @Assert\NotBlank()
     * @Assert\NotEqualTo(value=0)
     * @Assert\LessThan(value=100000000000)
     * @var integer $currentVersion
     */
    protected $currentVersion = 1;
    
    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="json_array")
     * @Assert\NotNull()
     * @Assert\Type(type="array")
     * @var array $versionData
     */
    protected $versionData = [];
    
    
    /**
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @Gedmo\Slug(fields={"title"}, updatable=true, unique=true, separator="-", style="lower", handlers={
      *     @Gedmo\SlugHandler(class="Gedmo\Sluggable\Handler\TreeSlugHandler", options={
      *         @Gedmo\SlugHandlerOption(name="parentRelationField", value="parent")
      *         @Gedmo\SlugHandlerOption(name="separator", value="/")
      *     })
      * })
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Length(min="1", max="255")
     * @var string $slug
     */
    protected $slug;
    
    /**
     * Used locale to override Translation listener's locale.
     * this is not a mapped field of entity metadata, just a simple property.
     *
     * @Gedmo\Versioned
     * @Assert\Locale()
     * @Gedmo\Locale
     * @var string $locale
     */
    protected $locale;
    
    /**
     * @Gedmo\Versioned
     * @Gedmo\TreeLeft
     * @ORM\Column(type="integer")
     * @Assert\Type(type="integer")
     * @var integer $lft
     */
    protected $lft;
    
    /**
     * @Gedmo\Versioned
     * @Gedmo\TreeLevel
     * @ORM\Column(type="integer")
     * @Assert\Type(type="integer")
     * @var integer $lvl
     */
    protected $lvl;
    
    /**
     * @Gedmo\Versioned
     * @Gedmo\TreeRight
     * @ORM\Column(type="integer")
     * @Assert\Type(type="integer")
     * @var integer $rgt
     */
    protected $rgt;
    
    /**
     * @Gedmo\Versioned
     * @Gedmo\TreeRoot
     * @ORM\Column(type="integer", nullable=true)
     * @var integer $root
     */
    protected $root;
    
    /**
     * Bidirectional - Many children [page] are linked by one parent [page] (OWNING SIDE).
     *
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="\Zikula\ContentModule\Entity\PageEntity", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     * @var \Zikula\ContentModule\Entity\PageEntity $parent
     */
    protected $parent;
    
    /**
     * Bidirectional - One parent [page] has many children [page] (INVERSE SIDE).
     *
     * @ORM\OneToMany(targetEntity="\Zikula\ContentModule\Entity\PageEntity", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     * @var \Zikula\ContentModule\Entity\PageEntity $children
     */
    protected $children;
    
    /**
     * @ORM\OneToMany(targetEntity="\Zikula\ContentModule\Entity\PageCategoryEntity", 
     *                mappedBy="entity", cascade={"all"}, 
     *                orphanRemoval=true)
     * @var \Zikula\ContentModule\Entity\PageCategoryEntity
     */
    protected $categories = null;
    
    /**
     * Bidirectional - One page [page] has many contentItems [content items] (INVERSE SIDE).
     *
     * @ORM\OneToMany(targetEntity="Zikula\ContentModule\Entity\ContentItemEntity", mappedBy="page", cascade={"remove", "detach"})
     * @ORM\JoinTable(name="zikula_content_pagecontentitems")
     * @ORM\OrderBy({"areaIndex" = "ASC", "areaPosition" = "ASC"})
     * @var \Zikula\ContentModule\Entity\ContentItemEntity[] $contentItems
     */
    protected $contentItems = null;
    
    
    /**
     * PageEntity constructor.
     *
     * Will not be called by Doctrine and can therefore be used
     * for own implementation purposes. It is also possible to add
     * arbitrary arguments as with every other class method.
     */
    public function __construct()
    {
        $this->contentItems = new ArrayCollection();
        $this->categories = new ArrayCollection();
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
     * Returns the title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * Sets the title.
     *
     * @param string $title
     *
     * @return void
     */
    public function setTitle($title)
    {
        if ($this->title !== $title) {
            $this->title = isset($title) ? $title : '';
        }
    }
    
    /**
     * Returns the show title.
     *
     * @return boolean
     */
    public function getShowTitle()
    {
        return $this->showTitle;
    }
    
    /**
     * Sets the show title.
     *
     * @param boolean $showTitle
     *
     * @return void
     */
    public function setShowTitle($showTitle)
    {
        if (boolval($this->showTitle) !== boolval($showTitle)) {
            $this->showTitle = boolval($showTitle);
        }
    }
    
    /**
     * Returns the meta description.
     *
     * @return text
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }
    
    /**
     * Sets the meta description.
     *
     * @param text $metaDescription
     *
     * @return void
     */
    public function setMetaDescription($metaDescription)
    {
        if ($this->metaDescription !== $metaDescription) {
            $this->metaDescription = isset($metaDescription) ? $metaDescription : '';
        }
    }
    
    /**
     * Returns the skip ui hook subscriber.
     *
     * @return boolean
     */
    public function getSkipUiHookSubscriber()
    {
        return $this->skipUiHookSubscriber;
    }
    
    /**
     * Sets the skip ui hook subscriber.
     *
     * @param boolean $skipUiHookSubscriber
     *
     * @return void
     */
    public function setSkipUiHookSubscriber($skipUiHookSubscriber)
    {
        if (boolval($this->skipUiHookSubscriber) !== boolval($skipUiHookSubscriber)) {
            $this->skipUiHookSubscriber = boolval($skipUiHookSubscriber);
        }
    }
    
    /**
     * Returns the skip filter hook subscriber.
     *
     * @return boolean
     */
    public function getSkipFilterHookSubscriber()
    {
        return $this->skipFilterHookSubscriber;
    }
    
    /**
     * Sets the skip filter hook subscriber.
     *
     * @param boolean $skipFilterHookSubscriber
     *
     * @return void
     */
    public function setSkipFilterHookSubscriber($skipFilterHookSubscriber)
    {
        if (boolval($this->skipFilterHookSubscriber) !== boolval($skipFilterHookSubscriber)) {
            $this->skipFilterHookSubscriber = boolval($skipFilterHookSubscriber);
        }
    }
    
    /**
     * Returns the layout.
     *
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }
    
    /**
     * Sets the layout.
     *
     * @param string $layout
     *
     * @return void
     */
    public function setLayout($layout)
    {
        if ($this->layout !== $layout) {
            $this->layout = isset($layout) ? $layout : '';
        }
    }
    
    /**
     * Returns the views.
     *
     * @return integer
     */
    public function getViews()
    {
        return $this->views;
    }
    
    /**
     * Sets the views.
     *
     * @param integer $views
     *
     * @return void
     */
    public function setViews($views)
    {
        if (intval($this->views) !== intval($views)) {
            $this->views = intval($views);
        }
    }
    
    /**
     * Returns the active.
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }
    
    /**
     * Sets the active.
     *
     * @param boolean $active
     *
     * @return void
     */
    public function setActive($active)
    {
        if (boolval($this->active) !== boolval($active)) {
            $this->active = boolval($active);
        }
    }
    
    /**
     * Returns the active from.
     *
     * @return DateTime
     */
    public function getActiveFrom()
    {
        return $this->activeFrom;
    }
    
    /**
     * Sets the active from.
     *
     * @param DateTime $activeFrom
     *
     * @return void
     */
    public function setActiveFrom($activeFrom)
    {
        if ($this->activeFrom !== $activeFrom) {
            if (!(null == $activeFrom && empty($activeFrom)) && !(is_object($activeFrom) && $activeFrom instanceOf \DateTimeInterface)) {
                $activeFrom = new \DateTime($activeFrom);
            }
            
            if (null === $activeFrom || empty($activeFrom)) {
                $activeFrom = new \DateTime();
            }
            
            if ($this->activeFrom != $activeFrom) {
                $this->activeFrom = $activeFrom;
            }
        }
    }
    
    /**
     * Returns the active to.
     *
     * @return DateTime
     */
    public function getActiveTo()
    {
        return $this->activeTo;
    }
    
    /**
     * Sets the active to.
     *
     * @param DateTime $activeTo
     *
     * @return void
     */
    public function setActiveTo($activeTo)
    {
        if ($this->activeTo !== $activeTo) {
            if (!(null == $activeTo && empty($activeTo)) && !(is_object($activeTo) && $activeTo instanceOf \DateTimeInterface)) {
                $activeTo = new \DateTime($activeTo);
            }
            
            if (null === $activeTo || empty($activeTo)) {
                $activeTo = new \DateTime();
            }
            
            if ($this->activeTo != $activeTo) {
                $this->activeTo = $activeTo;
            }
        }
    }
    
    /**
     * Returns the in menu.
     *
     * @return boolean
     */
    public function getInMenu()
    {
        return $this->inMenu;
    }
    
    /**
     * Sets the in menu.
     *
     * @param boolean $inMenu
     *
     * @return void
     */
    public function setInMenu($inMenu)
    {
        if (boolval($this->inMenu) !== boolval($inMenu)) {
            $this->inMenu = boolval($inMenu);
        }
    }
    
    /**
     * Returns the page language.
     *
     * @return string
     */
    public function getPageLanguage()
    {
        return $this->pageLanguage;
    }
    
    /**
     * Sets the page language.
     *
     * @param string $pageLanguage
     *
     * @return void
     */
    public function setPageLanguage($pageLanguage)
    {
        if ($this->pageLanguage !== $pageLanguage) {
            $this->pageLanguage = isset($pageLanguage) ? $pageLanguage : '';
        }
    }
    
    /**
     * Returns the optional string 1.
     *
     * @return string
     */
    public function getOptionalString1()
    {
        return $this->optionalString1;
    }
    
    /**
     * Sets the optional string 1.
     *
     * @param string $optionalString1
     *
     * @return void
     */
    public function setOptionalString1($optionalString1)
    {
        if ($this->optionalString1 !== $optionalString1) {
            $this->optionalString1 = isset($optionalString1) ? $optionalString1 : '';
        }
    }
    
    /**
     * Returns the optional string 2.
     *
     * @return string
     */
    public function getOptionalString2()
    {
        return $this->optionalString2;
    }
    
    /**
     * Sets the optional string 2.
     *
     * @param string $optionalString2
     *
     * @return void
     */
    public function setOptionalString2($optionalString2)
    {
        if ($this->optionalString2 !== $optionalString2) {
            $this->optionalString2 = isset($optionalString2) ? $optionalString2 : '';
        }
    }
    
    /**
     * Returns the optional text.
     *
     * @return text
     */
    public function getOptionalText()
    {
        return $this->optionalText;
    }
    
    /**
     * Sets the optional text.
     *
     * @param text $optionalText
     *
     * @return void
     */
    public function setOptionalText($optionalText)
    {
        if ($this->optionalText !== $optionalText) {
            $this->optionalText = isset($optionalText) ? $optionalText : '';
        }
    }
    
    /**
     * Returns the current version.
     *
     * @return integer
     */
    public function getCurrentVersion()
    {
        return $this->currentVersion;
    }
    
    /**
     * Sets the current version.
     *
     * @param integer $currentVersion
     *
     * @return void
     */
    public function setCurrentVersion($currentVersion)
    {
        if (intval($this->currentVersion) !== intval($currentVersion)) {
            $this->currentVersion = intval($currentVersion);
        }
    }
    
    /**
     * Returns the version data.
     *
     * @return array
     */
    public function getVersionData()
    {
        return $this->versionData;
    }
    
    /**
     * Sets the version data.
     *
     * @param array $versionData
     *
     * @return void
     */
    public function setVersionData($versionData)
    {
        if ($this->versionData !== $versionData) {
            $this->versionData = isset($versionData) ? $versionData : '';
        }
    }
    
    /**
     * Returns the slug.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }
    
    /**
     * Sets the slug.
     *
     * @param string $slug
     *
     * @return void
     */
    public function setSlug($slug)
    {
        if ($this->slug != $slug) {
            $this->slug = $slug;
        }
    }
    
    /**
     * Returns the locale.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }
    
    /**
     * Sets the locale.
     *
     * @param string $locale
     *
     * @return void
     */
    public function setLocale($locale)
    {
        if ($this->locale != $locale) {
            $this->locale = $locale;
        }
    }
    
    /**
     * Returns the lft.
     *
     * @return integer
     */
    public function getLft()
    {
        return $this->lft;
    }
    
    /**
     * Sets the lft.
     *
     * @param integer $lft
     *
     * @return void
     */
    public function setLft($lft)
    {
        if ($this->lft != $lft) {
            $this->lft = $lft;
        }
    }
    
    /**
     * Returns the lvl.
     *
     * @return integer
     */
    public function getLvl()
    {
        return $this->lvl;
    }
    
    /**
     * Sets the lvl.
     *
     * @param integer $lvl
     *
     * @return void
     */
    public function setLvl($lvl)
    {
        if ($this->lvl != $lvl) {
            $this->lvl = $lvl;
        }
    }
    
    /**
     * Returns the rgt.
     *
     * @return integer
     */
    public function getRgt()
    {
        return $this->rgt;
    }
    
    /**
     * Sets the rgt.
     *
     * @param integer $rgt
     *
     * @return void
     */
    public function setRgt($rgt)
    {
        if ($this->rgt != $rgt) {
            $this->rgt = $rgt;
        }
    }
    
    /**
     * Returns the root.
     *
     * @return integer
     */
    public function getRoot()
    {
        return $this->root;
    }
    
    /**
     * Sets the root.
     *
     * @param integer $root
     *
     * @return void
     */
    public function setRoot($root)
    {
        if ($this->root != $root) {
            $this->root = $root;
        }
    }
    
    /**
     * Returns the parent.
     *
     * @return \Zikula\ContentModule\Entity\PageEntity
     */
    public function getParent()
    {
        return $this->parent;
    }
    
    /**
     * Sets the parent.
     *
     * @param \Zikula\ContentModule\Entity\PageEntity $parent
     *
     * @return void
     */
    public function setParent($parent = null)
    {
        if ($this->parent != $parent) {
            $this->parent = $parent;
        }
    }
    
    /**
     * Returns the children.
     *
     * @return array
     */
    public function getChildren()
    {
        return $this->children;
    }
    
    /**
     * Sets the children.
     *
     * @param array $children
     *
     * @return void
     */
    public function setChildren($children)
    {
        if ($this->children != $children) {
            $this->children = $children;
        }
    }
    
    /**
     * Returns the categories.
     *
     * @return ArrayCollection[]
     */
    public function getCategories()
    {
        return $this->categories;
    }
    
    
    /**
     * Sets the categories.
     *
     * @param ArrayCollection $categories List of categories
     *
     * @return void
     */
    public function setCategories(ArrayCollection $categories)
    {
        foreach ($this->categories as $category) {
            if (false === $key = $this->collectionContains($categories, $category)) {
                $this->categories->removeElement($category);
            } else {
                $categories->remove($key);
            }
        }
        foreach ($categories as $category) {
            $this->categories->add($category);
        }
    }
    
    /**
     * Checks if a collection contains an element based only on two criteria (categoryRegistryId, category).
     *
     * @param ArrayCollection $collection Given collection
     * @param \Zikula\ContentModule\Entity\PageCategoryEntity $element Element to search for
     *
     * @return bool|int
     */
    private function collectionContains(ArrayCollection $collection, \Zikula\ContentModule\Entity\PageCategoryEntity $element)
    {
        foreach ($collection as $key => $category) {
            /** @var \Zikula\ContentModule\Entity\PageCategoryEntity $category */
            if ($category->getCategoryRegistryId() == $element->getCategoryRegistryId()
                && $category->getCategory() == $element->getCategory()
            ) {
                return $key;
            }
        }
    
        return false;
    }
    
    /**
     * Returns the content items.
     *
     * @return \Zikula\ContentModule\Entity\ContentItemEntity[]
     */
    public function getContentItems()
    {
        return $this->contentItems;
    }
    
    /**
     * Sets the content items.
     *
     * @param \Zikula\ContentModule\Entity\ContentItemEntity[] $contentItems
     *
     * @return void
     */
    public function setContentItems($contentItems)
    {
        foreach ($this->contentItems as $contentItemSingle) {
            $this->removeContentItems($contentItemSingle);
        }
        foreach ($contentItems as $contentItemSingle) {
            $this->addContentItems($contentItemSingle);
        }
    }
    
    /**
     * Adds an instance of \Zikula\ContentModule\Entity\ContentItemEntity to the list of content items.
     *
     * @param \Zikula\ContentModule\Entity\ContentItemEntity $contentItem The instance to be added to the collection
     *
     * @return void
     */
    public function addContentItems(\Zikula\ContentModule\Entity\ContentItemEntity $contentItem)
    {
        $this->contentItems->add($contentItem);
        $contentItem->setPage($this);
    }
    
    /**
     * Removes an instance of \Zikula\ContentModule\Entity\ContentItemEntity from the list of content items.
     *
     * @param \Zikula\ContentModule\Entity\ContentItemEntity $contentItem The instance to be removed from the collection
     *
     * @return void
     */
    public function removeContentItems(\Zikula\ContentModule\Entity\ContentItemEntity $contentItem)
    {
        $this->contentItems->removeElement($contentItem);
        $contentItem->setPage(null);
    }
    
    
    
    /**
     * Creates url arguments array for easy creation of display urls.
     *
     * @return array List of resulting arguments
     */
    public function createUrlArgs()
    {
        return [
            'slug' => $this->getSlug()
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
        return 'zikulacontentmodule.ui_hooks.pages';
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
        return 'Page ' . $this->getKey() . ': ' . $this->getTitle();
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
    
    
        // clone categories
        $categories = $this->categories;
        $this->categories = new ArrayCollection();
        foreach ($categories as $c) {
            $newCat = clone $c;
            $this->categories->add($newCat);
            $newCat->setEntity($this);
        }
    }
}
