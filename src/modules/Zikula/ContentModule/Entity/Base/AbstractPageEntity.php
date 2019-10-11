<?php

declare(strict_types=1);

/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 * @link https://ziku.la
 * @version Generated by ModuleStudio 1.4.0 (https://modulestudio.de).
 */

namespace Zikula\ContentModule\Entity\Base;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @var int $id
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
     * @var bool $showTitle
     */
    protected $showTitle = true;
    
    /**
     * @Gedmo\Translatable
     * @ORM\Column(length=255)
     * @Assert\NotNull()
     * @Assert\Length(min="0", max="255")
     * @var string $metaDescription
     */
    protected $metaDescription = '';
    
    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var bool $skipHookSubscribers
     */
    protected $skipHookSubscribers = false;
    
    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="json_array")
     * @Assert\NotNull()
     * @Assert\Type(type="array")
     * @var array $layout
     */
    protected $layout = [];
    
    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="integer")
     * @Assert\Type(type="integer")
     * @Assert\NotNull()
     * @Assert\LessThan(value=100000000000)
     * @var int $views
     */
    protected $views = 0;
    
    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var bool $active
     */
    protected $active = true;
    
    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\DateTime()
     * @var \DateTime $activeFrom
     */
    protected $activeFrom;
    
    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\DateTime()
     * @Assert\Expression("!value or value > this.getActiveFrom()", message="The start must be before the end.")
     * @var \DateTime $activeTo
     */
    protected $activeTo;
    
    /**
     * As soon as at least one selected entry applies for the current user the page becomes visible.
     *
     * @Gedmo\Versioned
     * @ORM\Column(length=100)
     * @Assert\NotBlank()
     * @ContentAssert\ListEntry(entityName="page", propertyName="scope", multiple=true)
     * @var string $scope
     */
    protected $scope = '0';
    
    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var bool $inMenu
     */
    protected $inMenu = true;
    
    /**
     * @Gedmo\Translatable
     * @ORM\Column(length=255)
     * @Assert\NotNull()
     * @Assert\Length(min="0", max="255")
     * @var string $optionalString1
     */
    protected $optionalString1 = '';
    
    /**
     * @Gedmo\Translatable
     * @ORM\Column(length=255)
     * @Assert\NotNull()
     * @Assert\Length(min="0", max="255")
     * @var string $optionalString2
     */
    protected $optionalString2 = '';
    
    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="text", length=2000)
     * @Assert\NotNull()
     * @Assert\Length(min="0", max="2000")
     * @var string $optionalText
     */
    protected $optionalText = '';
    
    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="simple_array", nullable=true)
     * @Assert\Type(type="array")
     * @var array $stylingClasses
     */
    protected $stylingClasses = [];
    
    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="integer")
     * @ORM\Version
     * @Assert\Type(type="integer")
     * @Assert\NotBlank()
     * @Assert\NotEqualTo(value=0)
     * @Assert\LessThan(value=100000000000)
     * @var int $currentVersion
     */
    protected $currentVersion = 1;
    
    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="json_array")
     * @Assert\NotNull()
     * @Assert\Type(type="array")
     * @var array $contentData
     */
    protected $contentData = [];
    
    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="json_array")
     * @Assert\NotNull()
     * @Assert\Type(type="array")
     * @var array $translationData
     */
    protected $translationData = [];
    
    
    /**
     * @var string Description of currently executed action to be persisted in next log entry
     */
    protected $_actionDescriptionForLogEntry = '';
    
    /**
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @Gedmo\Slug(fields={"title"}, updatable=true, unique=true, separator="-", style="lower", handlers={
     *     @Gedmo\SlugHandler(class="Gedmo\Sluggable\Handler\TreeSlugHandler", options={
     *         @Gedmo\SlugHandlerOption(name="parentRelationField", value="parent"),
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
     * This is not a mapped field of entity metadata, just a simple property.
     *
     * @Assert\Locale()
     * @Gedmo\Locale
     * @var string $locale
     */
    protected $locale;
    
    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(type="integer")
     * @Assert\Type(type="int")
     * @var int $lft
     */
    protected $lft;
    
    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(type="integer")
     * @Assert\Type(type="int")
     * @var int $lvl
     */
    protected $lvl;
    
    /**
     * @Gedmo\TreeRight
     * @ORM\Column(type="integer")
     * @Assert\Type(type="int")
     * @var int $rgt
     */
    protected $rgt;
    
    /**
     * @Gedmo\TreeRoot
     * @ORM\Column(type="integer", nullable=true)
     * @var int $root
     */
    protected $root;
    
    /**
     * Bidirectional - Many children [page] are linked by one parent [page] (OWNING SIDE).
     *
     * @Gedmo\Versioned
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="\Zikula\ContentModule\Entity\PageEntity", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     * @var self $parent
     */
    protected $parent;
    
    /**
     * Bidirectional - One parent [page] has many children [page] (INVERSE SIDE).
     *
     * @ORM\OneToMany(targetEntity="\Zikula\ContentModule\Entity\PageEntity", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     * @var self $children
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
    
    public function get_objectType(): string
    {
        return $this->_objectType;
    }
    
    public function set_objectType(string $_objectType): void
    {
        if ($this->_objectType !== $_objectType) {
            $this->_objectType = $_objectType ?? '';
        }
    }
    
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function setId(int $id = null): void
    {
        if ((int)$this->id !== $id) {
            $this->id = $id;
        }
    }
    
    public function getWorkflowState(): string
    {
        return $this->workflowState;
    }
    
    public function setWorkflowState(string $workflowState): void
    {
        if ($this->workflowState !== $workflowState) {
            $this->workflowState = $workflowState ?? '';
        }
    }
    
    public function getTitle(): string
    {
        return $this->title;
    }
    
    public function setTitle(string $title): void
    {
        if ($this->title !== $title) {
            $this->title = $title ?? '';
        }
    }
    
    public function getShowTitle(): bool
    {
        return $this->showTitle;
    }
    
    public function setShowTitle(bool $showTitle): void
    {
        if ((bool)$this->showTitle !== $showTitle) {
            $this->showTitle = $showTitle;
        }
    }
    
    public function getMetaDescription(): string
    {
        return $this->metaDescription;
    }
    
    public function setMetaDescription(string $metaDescription): void
    {
        if ($this->metaDescription !== $metaDescription) {
            $this->metaDescription = $metaDescription ?? '';
        }
    }
    
    public function getSkipHookSubscribers(): bool
    {
        return $this->skipHookSubscribers;
    }
    
    public function setSkipHookSubscribers(bool $skipHookSubscribers): void
    {
        if ((bool)$this->skipHookSubscribers !== $skipHookSubscribers) {
            $this->skipHookSubscribers = $skipHookSubscribers;
        }
    }
    
    public function getLayout(): array
    {
        return $this->layout;
    }
    
    public function setLayout(array $layout): void
    {
        if ($this->layout !== $layout) {
            $this->layout = $layout ?? [];
        }
    }
    
    public function getViews(): int
    {
        return $this->views;
    }
    
    public function setViews(int $views): void
    {
        if ((int)$this->views !== $views) {
            $this->views = $views;
        }
    }
    
    public function getActive(): bool
    {
        return $this->active;
    }
    
    public function setActive(bool $active): void
    {
        if ((bool)$this->active !== $active) {
            $this->active = $active;
        }
    }
    
    public function getActiveFrom(): ?\DateTimeInterface
    {
        return $this->activeFrom;
    }
    
    public function setActiveFrom(\DateTimeInterface $activeFrom = null): void
    {
        if ($this->activeFrom !== $activeFrom) {
            if (!(null === $activeFrom && empty($activeFrom)) && !(is_object($activeFrom) && $activeFrom instanceOf \DateTimeInterface)) {
                $activeFrom = new \DateTime($activeFrom);
            }
            
            if ($this->activeFrom !== $activeFrom) {
                $this->activeFrom = $activeFrom;
            }
        }
    }
    
    public function getActiveTo(): ?\DateTimeInterface
    {
        return $this->activeTo;
    }
    
    public function setActiveTo(\DateTimeInterface $activeTo = null): void
    {
        if ($this->activeTo !== $activeTo) {
            if (!(null === $activeTo && empty($activeTo)) && !(is_object($activeTo) && $activeTo instanceOf \DateTimeInterface)) {
                $activeTo = new \DateTime($activeTo);
            }
            
            if ($this->activeTo !== $activeTo) {
                $this->activeTo = $activeTo;
            }
        }
    }
    
    public function getScope(): string
    {
        return $this->scope;
    }
    
    public function setScope(string $scope): void
    {
        if ($this->scope !== $scope) {
            $this->scope = $scope ?? '';
        }
    }
    
    public function getInMenu(): bool
    {
        return $this->inMenu;
    }
    
    public function setInMenu(bool $inMenu): void
    {
        if ((bool)$this->inMenu !== $inMenu) {
            $this->inMenu = $inMenu;
        }
    }
    
    public function getOptionalString1(): string
    {
        return $this->optionalString1;
    }
    
    public function setOptionalString1(string $optionalString1): void
    {
        if ($this->optionalString1 !== $optionalString1) {
            $this->optionalString1 = $optionalString1 ?? '';
        }
    }
    
    public function getOptionalString2(): string
    {
        return $this->optionalString2;
    }
    
    public function setOptionalString2(string $optionalString2): void
    {
        if ($this->optionalString2 !== $optionalString2) {
            $this->optionalString2 = $optionalString2 ?? '';
        }
    }
    
    public function getOptionalText(): string
    {
        return $this->optionalText;
    }
    
    public function setOptionalText(string $optionalText): void
    {
        if ($this->optionalText !== $optionalText) {
            $this->optionalText = $optionalText ?? '';
        }
    }
    
    public function getStylingClasses(): ?array
    {
        return $this->stylingClasses;
    }
    
    public function setStylingClasses(array $stylingClasses = null): void
    {
        if ($this->stylingClasses !== $stylingClasses) {
            $this->stylingClasses = $stylingClasses;
        }
    }
    
    public function getCurrentVersion(): int
    {
        return $this->currentVersion;
    }
    
    public function setCurrentVersion(int $currentVersion): void
    {
        if ((int)$this->currentVersion !== $currentVersion) {
            $this->currentVersion = $currentVersion;
        }
    }
    
    public function getContentData(): array
    {
        return $this->contentData;
    }
    
    public function setContentData(array $contentData): void
    {
        if ($this->contentData !== $contentData) {
            $this->contentData = $contentData ?? [];
        }
    }
    
    public function getTranslationData(): array
    {
        return $this->translationData;
    }
    
    public function setTranslationData(array $translationData): void
    {
        if ($this->translationData !== $translationData) {
            $this->translationData = $translationData ?? [];
        }
    }
    
    public function get_actionDescriptionForLogEntry(): string
    {
        return $this->_actionDescriptionForLogEntry;
    }
    
    public function set_actionDescriptionForLogEntry(string $_actionDescriptionForLogEntry): void
    {
        if ($this->_actionDescriptionForLogEntry !== $_actionDescriptionForLogEntry) {
            $this->_actionDescriptionForLogEntry = $_actionDescriptionForLogEntry ?? '';
        }
    }
    
    public function getSlug(): ?string
    {
        return $this->slug;
    }
    
    public function setSlug(string $slug = null): void
    {
        if ($this->slug !== $slug) {
            $this->slug = $slug;
        }
    }
    
    public function getLocale()
    {
        return $this->locale;
    }
    
    public function setLocale($locale = null): void
    {
        if ($this->locale !== $locale) {
            $this->locale = $locale;
        }
    }
    
    public function getLft(): ?int
    {
        return $this->lft;
    }
    
    public function setLft(int $lft = null): void
    {
        if ($this->lft !== $lft) {
            $this->lft = $lft;
        }
    }
    
    public function getLvl(): ?int
    {
        return $this->lvl;
    }
    
    public function setLvl(int $lvl = null): void
    {
        if ($this->lvl !== $lvl) {
            $this->lvl = $lvl;
        }
    }
    
    public function getRgt(): ?int
    {
        return $this->rgt;
    }
    
    public function setRgt(int $rgt = null): void
    {
        if ($this->rgt !== $rgt) {
            $this->rgt = $rgt;
        }
    }
    
    public function getRoot(): ?int
    {
        return $this->root;
    }
    
    public function setRoot(int $root = null): void
    {
        if ($this->root !== $root) {
            $this->root = $root;
        }
    }
    
    public function getParent(): ?self
    {
        return $this->parent;
    }
    
    public function setParent(self $parent = null): void
    {
        if ($this->parent !== $parent) {
            $this->parent = $parent;
        }
    }
    
    public function getChildren(): ?Collection
    {
        return $this->children;
    }
    
    public function setChildren(Collection $children = null): void
    {
        if ($this->children !== $children) {
            $this->children = $children;
        }
    }
    
    public function getCategories(): ?Collection
    {
        return $this->categories;
    }
    
    
    /**
     * Sets the categories.
     */
    public function setCategories(Collection $categories): void
    {
        foreach ($this->categories as $category) {
            if (false === ($key = $this->collectionContains($categories, $category))) {
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
     * @return bool|int
     */
    private function collectionContains(Collection $collection, \Zikula\ContentModule\Entity\PageCategoryEntity $element)
    {
        foreach ($collection as $key => $category) {
            /** @var \Zikula\ContentModule\Entity\PageCategoryEntity $category */
            if ($category->getCategoryRegistryId() === $element->getCategoryRegistryId()
                && $category->getCategory() === $element->getCategory()
            ) {
                return $key;
            }
        }
    
        return false;
    }
    
    public function getContentItems()
    {
        return $this->contentItems;
    }
    
    public function setContentItems($contentItems = null): void
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
     */
    public function addContentItems(\Zikula\ContentModule\Entity\ContentItemEntity $contentItem): void
    {
        $this->contentItems->add($contentItem);
        $contentItem->setPage($this);
    }
    
    /**
     * Removes an instance of \Zikula\ContentModule\Entity\ContentItemEntity from the list of content items.
     */
    public function removeContentItems(\Zikula\ContentModule\Entity\ContentItemEntity $contentItem): void
    {
        $this->contentItems->removeElement($contentItem);
        $contentItem->setPage(null);
    }
    
    
    
    /**
     * Creates url arguments array for easy creation of display urls.
     */
    public function createUrlArgs(bool $forEditing = false): array
    {
        if (true === $forEditing) {
            return [
                'id' => $this->getId(),
                'slug' => $this->getSlug()
            ];
        }
    
        return [
            'slug' => $this->getSlug()
        ];
    }
    
    /**
     * Returns the primary key.
     */
    public function getKey(): ?int
    {
        return $this->getId();
    }
    
    /**
     * Determines whether this entity supports hook subscribers or not.
     */
    public function supportsHookSubscribers(): bool
    {
        return true;
    }
    
    /**
     * Return lower case name of multiple items needed for hook areas.
     */
    public function getHookAreaPrefix(): string
    {
        return 'zikulacontentmodule.ui_hooks.pages';
    }
    
    /**
     * Returns an array of all related objects that need to be persisted after clone.
     */
    public function getRelatedObjectsToPersist(array &$objects = []): array
    {
        return [];
    }
    
    /**
     * ToString interceptor implementation.
     * This method is useful for debugging purposes.
     */
    public function __toString(): string
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
