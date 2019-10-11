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
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Symfony\Component\Validator\Constraints as Assert;
use Zikula\Core\Doctrine\EntityAccess;
use Zikula\ContentModule\Traits\StandardFieldsTrait;
use Zikula\ContentModule\Validator\Constraints as ContentAssert;

/**
 * Entity class that defines the entity structure and behaviours.
 *
 * This is the base entity class for content item entities.
 * The following annotation marks it as a mapped superclass so subclasses
 * inherit orm properties.
 *
 * @ORM\MappedSuperclass
 */
abstract class AbstractContentItemEntity extends EntityAccess implements Translatable
{
    /**
     * Hook standard fields behaviour embedding createdBy, updatedBy, createdDate, updatedDate fields.
     */
    use StandardFieldsTrait;

    /**
     * @var string The tablename this object maps to
     */
    protected $_objectType = 'contentItem';
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", unique=true)
     * @var int $id
     */
    protected $id = 0;
    
    /**
     * the current workflow state
     *
     * @ORM\Column(length=20)
     * @Assert\NotBlank()
     * @ContentAssert\ListEntry(entityName="contentItem", propertyName="workflowState", multiple=false)
     * @var string $workflowState
     */
    protected $workflowState = 'initial';
    
    /**
     * @ORM\Column(length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="255")
     * @var string $owningType
     */
    protected $owningType = '';
    
    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="array")
     * @Assert\NotNull()
     * @Assert\Type(type="array")
     * @var array $contentData
     */
    protected $contentData = [];
    
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var bool $active
     */
    protected $active = true;
    
    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\DateTime()
     * @var \DateTime $activeFrom
     */
    protected $activeFrom;
    
    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\DateTime()
     * @Assert\Expression("!value or value > this.getActiveFrom()", message="The start must be before the end.")
     * @var \DateTime $activeTo
     */
    protected $activeTo;
    
    /**
     * As soon as at least one selected entry applies for the current user the content becomes visible.
     *
     * @ORM\Column(length=100)
     * @Assert\NotBlank()
     * @ContentAssert\ListEntry(entityName="contentItem", propertyName="scope", multiple=true)
     * @var string $scope
     */
    protected $scope = '0';
    
    /**
     * @ORM\Column(type="simple_array", nullable=true)
     * @Assert\Type(type="array")
     * @var array $stylingClasses
     */
    protected $stylingClasses = [];
    
    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="text", length=100000)
     * @Assert\NotNull()
     * @Assert\Length(min="0", max="100000")
     * @var string $searchText
     */
    protected $searchText = '';
    
    /**
     * @Gedmo\Translatable
     * @ORM\Column(length=255)
     * @Assert\NotNull()
     * @Assert\Length(min="0", max="255")
     * @var string $additionalSearchText
     */
    protected $additionalSearchText = '';
    
    
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
     * Bidirectional - Many contentItems [content items] are linked by one page [page] (OWNING SIDE).
     *
     * @ORM\ManyToOne(targetEntity="Zikula\ContentModule\Entity\PageEntity", inversedBy="contentItems", cascade={"persist"})
     * @ORM\JoinTable(name="zikula_content_page")
     * @Assert\Type(type="Zikula\ContentModule\Entity\PageEntity")
     * @var \Zikula\ContentModule\Entity\PageEntity $page
     */
    protected $page;
    
    
    /**
     * ContentItemEntity constructor.
     *
     * Will not be called by Doctrine and can therefore be used
     * for own implementation purposes. It is also possible to add
     * arbitrary arguments as with every other class method.
     */
    public function __construct()
    {
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
    
    public function getOwningType(): string
    {
        return $this->owningType;
    }
    
    public function setOwningType(string $owningType): void
    {
        if ($this->owningType !== $owningType) {
            $this->owningType = $owningType ?? '';
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
    
    public function getSearchText(): string
    {
        return $this->searchText;
    }
    
    public function setSearchText(string $searchText): void
    {
        if ($this->searchText !== $searchText) {
            $this->searchText = $searchText ?? '';
        }
    }
    
    public function getAdditionalSearchText(): string
    {
        return $this->additionalSearchText;
    }
    
    public function setAdditionalSearchText(string $additionalSearchText): void
    {
        if ($this->additionalSearchText !== $additionalSearchText) {
            $this->additionalSearchText = $additionalSearchText ?? '';
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
    
    
    public function getPage(): ?\Zikula\ContentModule\Entity\PageEntity
    {
        return $this->page;
    }
    
    public function setPage(\Zikula\ContentModule\Entity\PageEntity $page = null): void
    {
        $this->page = $page;
    }
    
    
    
    /**
     * Creates url arguments array for easy creation of display urls.
     */
    public function createUrlArgs(): array
    {
        return [
            'id' => $this->getId()
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
        return 'zikulacontentmodule.ui_hooks.contentitems';
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
        return 'Content item ' . $this->getKey() . ': ' . $this->getOwningType();
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
