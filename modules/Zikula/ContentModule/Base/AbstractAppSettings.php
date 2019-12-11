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

namespace Zikula\ContentModule\Base;

use Symfony\Component\Validator\Constraints as Assert;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\ContentModule\Entity\Factory\EntityFactory;
use Zikula\ContentModule\Validator\Constraints as ContentAssert;

/**
 * Application settings class for handling module variables.
 */
abstract class AbstractAppSettings
{
    /**
     * @var VariableApiInterface
     */
    protected $variableApi;
    
    /**
     * @var EntityFactory
     */
    protected $entityFactory;
    
    /**
     * @Assert\NotBlank()
     * @ContentAssert\ListEntry(entityName="appSettings", propertyName="stateOfNewPages", multiple=false)
     * @var string $stateOfNewPages
     */
    protected $stateOfNewPages = '1';
    
    /**
     * Page views are only counted when the user has no edit access. Enable if you want to use the block showing most viewed pages.
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var bool $countPageViews
     */
    protected $countPageViews = false;
    
    /**
     * If you want to use Google maps you need an API key for it. You should enable both "Maps JavaScript API" and "Maps Static API".
     *
     * @Assert\NotNull()
     * @Assert\Length(min="0", max="255")
     * @var string $googleMapsApiKey
     */
    protected $googleMapsApiKey = '';
    
    /**
     * If you want to get translation support by Yandex which can provide suggestions you need an API key for it.
     *
     * @Assert\NotNull()
     * @Assert\Length(min="0", max="255")
     * @var string $yandexTranslateApiKey
     */
    protected $yandexTranslateApiKey = '';
    
    /**
     * Whether to enable the unfiltered raw text plugin. Use this plugin with caution and if you can trust your editors, since no filtering is being done on the content. To be used for iframes, JavaScript blocks, etc.
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var bool $enableRawPlugin
     */
    protected $enableRawPlugin = false;
    
    /**
     * Whether to inherit permissions from parent to child pages or not.
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var bool $inheritPermissions
     */
    protected $inheritPermissions = false;
    
    /**
     * Whether page titles should automatically be linked using MultiHook.
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var bool $enableAutomaticPageLinks
     */
    protected $enableAutomaticPageLinks = true;
    
    /**
     * A list of CSS class names available for styling pages - for example "product" or "legal".
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="5000")
     * @var string $pageStyles
     */
    protected $pageStyles = 'dummy|Dummy';
    
    /**
     * A list of CSS class names available for styling page sections - for example "header" or "reference-row".
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="5000")
     * @var string $sectionStyles
     */
    protected $sectionStyles = 'dummy|Dummy';
    
    /**
     * A list of CSS class names available for styling single content elements - for instance "note" or "shadow".
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="5000")
     * @var string $contentStyles
     */
    protected $contentStyles = 'dummy|Dummy';
    
    /**
     * If you need an additional string for each page you can enable an optional field.
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var bool $enableOptionalString1
     */
    protected $enableOptionalString1 = false;
    
    /**
     * If you need an additional string for each page you can enable an optional field.
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var bool $enableOptionalString2
     */
    protected $enableOptionalString2 = false;
    
    /**
     * If you need an additional text for each page you can enable an optional field.
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var bool $enableOptionalText
     */
    protected $enableOptionalText = false;
    
    /**
     * This removes the module name (defaults to "content") from permalinks.
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var bool $ignoreBundleNameInRoutes
     */
    protected $ignoreBundleNameInRoutes = true;
    
    /**
     * This removes the primary entity name ("page") from permalinks.
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var bool $ignoreEntityNameInRoutes
     */
    protected $ignoreEntityNameInRoutes = true;
    
    /**
     * This removes the first tree level of pages from permalinks of pages in greater levels. If enabled first level pages act only as dummys while second level pages are the actual main pages. Recommended because it allows working with only one single tree of pages.
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var bool $ignoreFirstTreeLevelInRoutes
     */
    protected $ignoreFirstTreeLevelInRoutes = true;
    
    /**
     * @Assert\NotBlank()
     * @ContentAssert\ListEntry(entityName="appSettings", propertyName="permalinkSuffix", multiple=false)
     * @var string $permalinkSuffix
     */
    protected $permalinkSuffix = 'none';
    
    /**
     * The amount of pages shown per page
     *
     * @Assert\Type(type="integer")
     * @Assert\NotBlank()
     * @Assert\NotEqualTo(value=0)
     * @Assert\LessThan(value=100000000000)
     * @var int $pageEntriesPerPage
     */
    protected $pageEntriesPerPage = 10;
    
    /**
     * Whether to add a link to pages of the current user on his account page
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var bool $linkOwnPagesOnAccountPage
     */
    protected $linkOwnPagesOnAccountPage = true;
    
    /**
     * Whether users may only see own pages
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var bool $pagePrivateMode
     */
    protected $pagePrivateMode = false;
    
    /**
     * Whether only own entries should be shown on view pages by default or not
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var bool $showOnlyOwnEntries
     */
    protected $showOnlyOwnEntries = false;
    
    /**
     * Whether to allow moderators choosing a user which will be set as creator.
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var bool $allowModerationSpecificCreatorForPage
     */
    protected $allowModerationSpecificCreatorForPage = false;
    
    /**
     * Whether to allow moderators choosing a custom creation date.
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var bool $allowModerationSpecificCreationDateForPage
     */
    protected $allowModerationSpecificCreationDateForPage = false;
    
    /**
     * Which sections are supported in the Finder component (used by Scribite plug-ins).
     *
     * @Assert\NotNull()
     * @ContentAssert\ListEntry(entityName="appSettings", propertyName="enabledFinderTypes", multiple=true)
     * @var string $enabledFinderTypes
     */
    protected $enabledFinderTypes = 'page';
    
    /**
     * Adding a limitation to the revisioning will still keep the possibility to revert pages to an older version. You will loose the possibility to inspect changes done earlier than the oldest stored revision though.
     *
     * @Assert\NotBlank()
     * @ContentAssert\ListEntry(entityName="appSettings", propertyName="revisionHandlingForPage", multiple=false)
     * @var string $revisionHandlingForPage
     */
    protected $revisionHandlingForPage = 'unlimited';
    
    /**
     * @Assert\NotNull()
     * @ContentAssert\ListEntry(entityName="appSettings", propertyName="maximumAmountOfPageRevisions", multiple=false)
     * @var string $maximumAmountOfPageRevisions
     */
    protected $maximumAmountOfPageRevisions = '25';
    
    /**
     * @Assert\NotNull()
     * @Assert\Length(min="0", max="255")
     * @var string $periodForPageRevisions
     */
    protected $periodForPageRevisions = 'P1Y0M0DT0H0M0S';
    
    /**
     * Whether to show the version history to editors or not.
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var bool $showPageHistory
     */
    protected $showPageHistory = true;
    
    
    public function __construct(
        VariableApiInterface $variableApi,
        EntityFactory $entityFactory
    ) {
        $this->variableApi = $variableApi;
        $this->entityFactory = $entityFactory;
    
        $this->load();
    }
    
    public function getStateOfNewPages(): string
    {
        return $this->stateOfNewPages;
    }
    
    public function setStateOfNewPages(string $stateOfNewPages): void
    {
        if ($this->stateOfNewPages !== $stateOfNewPages) {
            $this->stateOfNewPages = $stateOfNewPages ?? '';
        }
    }
    
    public function getCountPageViews(): bool
    {
        return $this->countPageViews;
    }
    
    public function setCountPageViews(bool $countPageViews): void
    {
        if ((bool)$this->countPageViews !== $countPageViews) {
            $this->countPageViews = $countPageViews;
        }
    }
    
    public function getGoogleMapsApiKey(): string
    {
        return $this->googleMapsApiKey;
    }
    
    public function setGoogleMapsApiKey(string $googleMapsApiKey): void
    {
        if ($this->googleMapsApiKey !== $googleMapsApiKey) {
            $this->googleMapsApiKey = $googleMapsApiKey ?? '';
        }
    }
    
    public function getYandexTranslateApiKey(): string
    {
        return $this->yandexTranslateApiKey;
    }
    
    public function setYandexTranslateApiKey(string $yandexTranslateApiKey): void
    {
        if ($this->yandexTranslateApiKey !== $yandexTranslateApiKey) {
            $this->yandexTranslateApiKey = $yandexTranslateApiKey ?? '';
        }
    }
    
    public function getEnableRawPlugin(): bool
    {
        return $this->enableRawPlugin;
    }
    
    public function setEnableRawPlugin(bool $enableRawPlugin): void
    {
        if ((bool)$this->enableRawPlugin !== $enableRawPlugin) {
            $this->enableRawPlugin = $enableRawPlugin;
        }
    }
    
    public function getInheritPermissions(): bool
    {
        return $this->inheritPermissions;
    }
    
    public function setInheritPermissions(bool $inheritPermissions): void
    {
        if ((bool)$this->inheritPermissions !== $inheritPermissions) {
            $this->inheritPermissions = $inheritPermissions;
        }
    }
    
    public function getEnableAutomaticPageLinks(): bool
    {
        return $this->enableAutomaticPageLinks;
    }
    
    public function setEnableAutomaticPageLinks(bool $enableAutomaticPageLinks): void
    {
        if ((bool)$this->enableAutomaticPageLinks !== $enableAutomaticPageLinks) {
            $this->enableAutomaticPageLinks = $enableAutomaticPageLinks;
        }
    }
    
    public function getPageStyles(): string
    {
        return $this->pageStyles;
    }
    
    public function setPageStyles(string $pageStyles): void
    {
        if ($this->pageStyles !== $pageStyles) {
            $this->pageStyles = $pageStyles ?? '';
        }
    }
    
    public function getSectionStyles(): string
    {
        return $this->sectionStyles;
    }
    
    public function setSectionStyles(string $sectionStyles): void
    {
        if ($this->sectionStyles !== $sectionStyles) {
            $this->sectionStyles = $sectionStyles ?? '';
        }
    }
    
    public function getContentStyles(): string
    {
        return $this->contentStyles;
    }
    
    public function setContentStyles(string $contentStyles): void
    {
        if ($this->contentStyles !== $contentStyles) {
            $this->contentStyles = $contentStyles ?? '';
        }
    }
    
    public function getEnableOptionalString1(): bool
    {
        return $this->enableOptionalString1;
    }
    
    public function setEnableOptionalString1(bool $enableOptionalString1): void
    {
        if ((bool)$this->enableOptionalString1 !== $enableOptionalString1) {
            $this->enableOptionalString1 = $enableOptionalString1;
        }
    }
    
    public function getEnableOptionalString2(): bool
    {
        return $this->enableOptionalString2;
    }
    
    public function setEnableOptionalString2(bool $enableOptionalString2): void
    {
        if ((bool)$this->enableOptionalString2 !== $enableOptionalString2) {
            $this->enableOptionalString2 = $enableOptionalString2;
        }
    }
    
    public function getEnableOptionalText(): bool
    {
        return $this->enableOptionalText;
    }
    
    public function setEnableOptionalText(bool $enableOptionalText): void
    {
        if ((bool)$this->enableOptionalText !== $enableOptionalText) {
            $this->enableOptionalText = $enableOptionalText;
        }
    }
    
    public function getIgnoreBundleNameInRoutes(): bool
    {
        return $this->ignoreBundleNameInRoutes;
    }
    
    public function setIgnoreBundleNameInRoutes(bool $ignoreBundleNameInRoutes): void
    {
        if ((bool)$this->ignoreBundleNameInRoutes !== $ignoreBundleNameInRoutes) {
            $this->ignoreBundleNameInRoutes = $ignoreBundleNameInRoutes;
        }
    }
    
    public function getIgnoreEntityNameInRoutes(): bool
    {
        return $this->ignoreEntityNameInRoutes;
    }
    
    public function setIgnoreEntityNameInRoutes(bool $ignoreEntityNameInRoutes): void
    {
        if ((bool)$this->ignoreEntityNameInRoutes !== $ignoreEntityNameInRoutes) {
            $this->ignoreEntityNameInRoutes = $ignoreEntityNameInRoutes;
        }
    }
    
    public function getIgnoreFirstTreeLevelInRoutes(): bool
    {
        return $this->ignoreFirstTreeLevelInRoutes;
    }
    
    public function setIgnoreFirstTreeLevelInRoutes(bool $ignoreFirstTreeLevelInRoutes): void
    {
        if ((bool)$this->ignoreFirstTreeLevelInRoutes !== $ignoreFirstTreeLevelInRoutes) {
            $this->ignoreFirstTreeLevelInRoutes = $ignoreFirstTreeLevelInRoutes;
        }
    }
    
    public function getPermalinkSuffix(): string
    {
        return $this->permalinkSuffix;
    }
    
    public function setPermalinkSuffix(string $permalinkSuffix): void
    {
        if ($this->permalinkSuffix !== $permalinkSuffix) {
            $this->permalinkSuffix = $permalinkSuffix ?? '';
        }
    }
    
    public function getPageEntriesPerPage(): int
    {
        return $this->pageEntriesPerPage;
    }
    
    public function setPageEntriesPerPage(int $pageEntriesPerPage): void
    {
        if ((int)$this->pageEntriesPerPage !== $pageEntriesPerPage) {
            $this->pageEntriesPerPage = $pageEntriesPerPage;
        }
    }
    
    public function getLinkOwnPagesOnAccountPage(): bool
    {
        return $this->linkOwnPagesOnAccountPage;
    }
    
    public function setLinkOwnPagesOnAccountPage(bool $linkOwnPagesOnAccountPage): void
    {
        if ((bool)$this->linkOwnPagesOnAccountPage !== $linkOwnPagesOnAccountPage) {
            $this->linkOwnPagesOnAccountPage = $linkOwnPagesOnAccountPage;
        }
    }
    
    public function getPagePrivateMode(): bool
    {
        return $this->pagePrivateMode;
    }
    
    public function setPagePrivateMode(bool $pagePrivateMode): void
    {
        if ((bool)$this->pagePrivateMode !== $pagePrivateMode) {
            $this->pagePrivateMode = $pagePrivateMode;
        }
    }
    
    public function getShowOnlyOwnEntries(): bool
    {
        return $this->showOnlyOwnEntries;
    }
    
    public function setShowOnlyOwnEntries(bool $showOnlyOwnEntries): void
    {
        if ((bool)$this->showOnlyOwnEntries !== $showOnlyOwnEntries) {
            $this->showOnlyOwnEntries = $showOnlyOwnEntries;
        }
    }
    
    public function getAllowModerationSpecificCreatorForPage(): bool
    {
        return $this->allowModerationSpecificCreatorForPage;
    }
    
    public function setAllowModerationSpecificCreatorForPage(bool $allowModerationSpecificCreatorForPage): void
    {
        if ((bool)$this->allowModerationSpecificCreatorForPage !== $allowModerationSpecificCreatorForPage) {
            $this->allowModerationSpecificCreatorForPage = $allowModerationSpecificCreatorForPage;
        }
    }
    
    public function getAllowModerationSpecificCreationDateForPage(): bool
    {
        return $this->allowModerationSpecificCreationDateForPage;
    }
    
    public function setAllowModerationSpecificCreationDateForPage(bool $allowModerationSpecificCreationDateForPage): void
    {
        if ((bool)$this->allowModerationSpecificCreationDateForPage !== $allowModerationSpecificCreationDateForPage) {
            $this->allowModerationSpecificCreationDateForPage = $allowModerationSpecificCreationDateForPage;
        }
    }
    
    public function getEnabledFinderTypes(): string
    {
        return $this->enabledFinderTypes;
    }
    
    public function setEnabledFinderTypes(string $enabledFinderTypes): void
    {
        if ($this->enabledFinderTypes !== $enabledFinderTypes) {
            $this->enabledFinderTypes = $enabledFinderTypes ?? '';
        }
    }
    
    public function getRevisionHandlingForPage(): string
    {
        return $this->revisionHandlingForPage;
    }
    
    public function setRevisionHandlingForPage(string $revisionHandlingForPage): void
    {
        if ($this->revisionHandlingForPage !== $revisionHandlingForPage) {
            $this->revisionHandlingForPage = $revisionHandlingForPage ?? '';
        }
    }
    
    public function getMaximumAmountOfPageRevisions(): string
    {
        return $this->maximumAmountOfPageRevisions;
    }
    
    public function setMaximumAmountOfPageRevisions(string $maximumAmountOfPageRevisions): void
    {
        if ($this->maximumAmountOfPageRevisions !== $maximumAmountOfPageRevisions) {
            $this->maximumAmountOfPageRevisions = $maximumAmountOfPageRevisions ?? '';
        }
    }
    
    public function getPeriodForPageRevisions(): string
    {
        return $this->periodForPageRevisions;
    }
    
    public function setPeriodForPageRevisions(string $periodForPageRevisions): void
    {
        if ($this->periodForPageRevisions !== $periodForPageRevisions) {
            $this->periodForPageRevisions = $periodForPageRevisions ?? '';
        }
    }
    
    public function getShowPageHistory(): bool
    {
        return $this->showPageHistory;
    }
    
    public function setShowPageHistory(bool $showPageHistory): void
    {
        if ((bool)$this->showPageHistory !== $showPageHistory) {
            $this->showPageHistory = $showPageHistory;
        }
    }
    
    
    /**
     * Loads module variables from the database.
     */
    protected function load(): void
    {
        $moduleVars = $this->variableApi->getAll('ZikulaContentModule');
    
        if (isset($moduleVars['stateOfNewPages'])) {
            $this->setStateOfNewPages($moduleVars['stateOfNewPages']);
        }
        if (isset($moduleVars['countPageViews'])) {
            $this->setCountPageViews($moduleVars['countPageViews']);
        }
        if (isset($moduleVars['googleMapsApiKey'])) {
            $this->setGoogleMapsApiKey($moduleVars['googleMapsApiKey']);
        }
        if (isset($moduleVars['yandexTranslateApiKey'])) {
            $this->setYandexTranslateApiKey($moduleVars['yandexTranslateApiKey']);
        }
        if (isset($moduleVars['enableRawPlugin'])) {
            $this->setEnableRawPlugin($moduleVars['enableRawPlugin']);
        }
        if (isset($moduleVars['inheritPermissions'])) {
            $this->setInheritPermissions($moduleVars['inheritPermissions']);
        }
        if (isset($moduleVars['enableAutomaticPageLinks'])) {
            $this->setEnableAutomaticPageLinks($moduleVars['enableAutomaticPageLinks']);
        }
        if (isset($moduleVars['pageStyles'])) {
            $this->setPageStyles($moduleVars['pageStyles']);
        }
        if (isset($moduleVars['sectionStyles'])) {
            $this->setSectionStyles($moduleVars['sectionStyles']);
        }
        if (isset($moduleVars['contentStyles'])) {
            $this->setContentStyles($moduleVars['contentStyles']);
        }
        if (isset($moduleVars['enableOptionalString1'])) {
            $this->setEnableOptionalString1($moduleVars['enableOptionalString1']);
        }
        if (isset($moduleVars['enableOptionalString2'])) {
            $this->setEnableOptionalString2($moduleVars['enableOptionalString2']);
        }
        if (isset($moduleVars['enableOptionalText'])) {
            $this->setEnableOptionalText($moduleVars['enableOptionalText']);
        }
        if (isset($moduleVars['ignoreBundleNameInRoutes'])) {
            $this->setIgnoreBundleNameInRoutes($moduleVars['ignoreBundleNameInRoutes']);
        }
        if (isset($moduleVars['ignoreEntityNameInRoutes'])) {
            $this->setIgnoreEntityNameInRoutes($moduleVars['ignoreEntityNameInRoutes']);
        }
        if (isset($moduleVars['ignoreFirstTreeLevelInRoutes'])) {
            $this->setIgnoreFirstTreeLevelInRoutes($moduleVars['ignoreFirstTreeLevelInRoutes']);
        }
        if (isset($moduleVars['permalinkSuffix'])) {
            $this->setPermalinkSuffix($moduleVars['permalinkSuffix']);
        }
        if (isset($moduleVars['pageEntriesPerPage'])) {
            $this->setPageEntriesPerPage($moduleVars['pageEntriesPerPage']);
        }
        if (isset($moduleVars['linkOwnPagesOnAccountPage'])) {
            $this->setLinkOwnPagesOnAccountPage($moduleVars['linkOwnPagesOnAccountPage']);
        }
        if (isset($moduleVars['pagePrivateMode'])) {
            $this->setPagePrivateMode($moduleVars['pagePrivateMode']);
        }
        if (isset($moduleVars['showOnlyOwnEntries'])) {
            $this->setShowOnlyOwnEntries($moduleVars['showOnlyOwnEntries']);
        }
        if (isset($moduleVars['allowModerationSpecificCreatorForPage'])) {
            $this->setAllowModerationSpecificCreatorForPage($moduleVars['allowModerationSpecificCreatorForPage']);
        }
        if (isset($moduleVars['allowModerationSpecificCreationDateForPage'])) {
            $this->setAllowModerationSpecificCreationDateForPage($moduleVars['allowModerationSpecificCreationDateForPage']);
        }
        if (isset($moduleVars['enabledFinderTypes'])) {
            $this->setEnabledFinderTypes($moduleVars['enabledFinderTypes']);
        }
        if (isset($moduleVars['revisionHandlingForPage'])) {
            $this->setRevisionHandlingForPage($moduleVars['revisionHandlingForPage']);
        }
        if (isset($moduleVars['maximumAmountOfPageRevisions'])) {
            $this->setMaximumAmountOfPageRevisions($moduleVars['maximumAmountOfPageRevisions']);
        }
        if (isset($moduleVars['periodForPageRevisions'])) {
            $this->setPeriodForPageRevisions($moduleVars['periodForPageRevisions']);
        }
        if (isset($moduleVars['showPageHistory'])) {
            $this->setShowPageHistory($moduleVars['showPageHistory']);
        }
    }
    
    /**
     * Saves module variables into the database.
     */
    public function save(): void
    {
        $this->variableApi->set('ZikulaContentModule', 'stateOfNewPages', $this->getStateOfNewPages());
        $this->variableApi->set('ZikulaContentModule', 'countPageViews', $this->getCountPageViews());
        $this->variableApi->set('ZikulaContentModule', 'googleMapsApiKey', $this->getGoogleMapsApiKey());
        $this->variableApi->set('ZikulaContentModule', 'yandexTranslateApiKey', $this->getYandexTranslateApiKey());
        $this->variableApi->set('ZikulaContentModule', 'enableRawPlugin', $this->getEnableRawPlugin());
        $this->variableApi->set('ZikulaContentModule', 'inheritPermissions', $this->getInheritPermissions());
        $this->variableApi->set('ZikulaContentModule', 'enableAutomaticPageLinks', $this->getEnableAutomaticPageLinks());
        $this->variableApi->set('ZikulaContentModule', 'pageStyles', $this->getPageStyles());
        $this->variableApi->set('ZikulaContentModule', 'sectionStyles', $this->getSectionStyles());
        $this->variableApi->set('ZikulaContentModule', 'contentStyles', $this->getContentStyles());
        $this->variableApi->set('ZikulaContentModule', 'enableOptionalString1', $this->getEnableOptionalString1());
        $this->variableApi->set('ZikulaContentModule', 'enableOptionalString2', $this->getEnableOptionalString2());
        $this->variableApi->set('ZikulaContentModule', 'enableOptionalText', $this->getEnableOptionalText());
        $this->variableApi->set('ZikulaContentModule', 'ignoreBundleNameInRoutes', $this->getIgnoreBundleNameInRoutes());
        $this->variableApi->set('ZikulaContentModule', 'ignoreEntityNameInRoutes', $this->getIgnoreEntityNameInRoutes());
        $this->variableApi->set('ZikulaContentModule', 'ignoreFirstTreeLevelInRoutes', $this->getIgnoreFirstTreeLevelInRoutes());
        $this->variableApi->set('ZikulaContentModule', 'permalinkSuffix', $this->getPermalinkSuffix());
        $this->variableApi->set('ZikulaContentModule', 'pageEntriesPerPage', $this->getPageEntriesPerPage());
        $this->variableApi->set('ZikulaContentModule', 'linkOwnPagesOnAccountPage', $this->getLinkOwnPagesOnAccountPage());
        $this->variableApi->set('ZikulaContentModule', 'pagePrivateMode', $this->getPagePrivateMode());
        $this->variableApi->set('ZikulaContentModule', 'showOnlyOwnEntries', $this->getShowOnlyOwnEntries());
        $this->variableApi->set('ZikulaContentModule', 'allowModerationSpecificCreatorForPage', $this->getAllowModerationSpecificCreatorForPage());
        $this->variableApi->set('ZikulaContentModule', 'allowModerationSpecificCreationDateForPage', $this->getAllowModerationSpecificCreationDateForPage());
        $this->variableApi->set('ZikulaContentModule', 'enabledFinderTypes', $this->getEnabledFinderTypes());
        $this->variableApi->set('ZikulaContentModule', 'revisionHandlingForPage', $this->getRevisionHandlingForPage());
        $this->variableApi->set('ZikulaContentModule', 'maximumAmountOfPageRevisions', $this->getMaximumAmountOfPageRevisions());
        $this->variableApi->set('ZikulaContentModule', 'periodForPageRevisions', $this->getPeriodForPageRevisions());
        $this->variableApi->set('ZikulaContentModule', 'showPageHistory', $this->getShowPageHistory());
    
        $entityManager = $this->entityFactory->getEntityManager();
        $revisionHandling = $this->getRevisionHandlingForPage();
        $limitParameter = '';
        if ('limitedByAmount' === $revisionHandling) {
            $limitParameter = $this->getMaximumAmountOfPageRevisions();
        } elseif ('limitedByDate' === $revisionHandling) {
            $limitParameter = $this->getPeriodForPageRevisions();
        }
    
        $logEntriesRepository = $entityManager->getRepository('ZikulaContentModule:PageLogEntryEntity');
        $logEntriesRepository->purgeHistory($revisionHandling, $limitParameter);
    }
}