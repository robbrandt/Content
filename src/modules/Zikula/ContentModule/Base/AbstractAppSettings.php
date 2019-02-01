<?php
/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 * @link https://ziku.la
 * @version Generated by ModuleStudio 1.4.0 (https://modulestudio.de).
 */

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
     * @var boolean $countPageViews
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
     * @var boolean $enableRawPlugin
     */
    protected $enableRawPlugin = false;
    
    /**
     * Whether to inherit permissions from parent to child pages or not.
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $inheritPermissions
     */
    protected $inheritPermissions = false;
    
    /**
     * A list of CSS class names available for styling pages - for example "product" or "legal".
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="5000")
     * @var text $pageStyles
     */
    protected $pageStyles = 'dummy|Dummy';
    
    /**
     * A list of CSS class names available for styling page sections - for example "header" or "reference-row".
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="5000")
     * @var text $sectionStyles
     */
    protected $sectionStyles = 'dummy|Dummy';
    
    /**
     * A list of CSS class names available for styling single content elements - for instance "note" or "shadow".
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="5000")
     * @var text $contentStyles
     */
    protected $contentStyles = 'dummy|Dummy';
    
    /**
     * If you need an additional string for each page you can enable an optional field.
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $enableOptionalString1
     */
    protected $enableOptionalString1 = false;
    
    /**
     * If you need an additional string for each page you can enable an optional field.
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $enableOptionalString2
     */
    protected $enableOptionalString2 = false;
    
    /**
     * If you need an additional text for each page you can enable an optional field.
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $enableOptionalText
     */
    protected $enableOptionalText = false;
    
    /**
     * This removes the module name (defaults to "content") from permalinks.
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $ignoreBundleNameInRoutes
     */
    protected $ignoreBundleNameInRoutes = true;
    
    /**
     * This removes the primary entity name ("page") from permalinks.
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $ignoreEntityNameInRoutes
     */
    protected $ignoreEntityNameInRoutes = true;
    
    /**
     * This removes the first tree level of pages from permalinks of pages in greater levels. If enabled first level pages act only as dummys while second level pages are the actual main pages. Recommended because it allows working with only one single tree of pages.
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $ignoreFirstTreeLevelInRoutes
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
     * @var integer $pageEntriesPerPage
     */
    protected $pageEntriesPerPage = 10;
    
    /**
     * Whether to add a link to pages of the current user on his account page
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $linkOwnPagesOnAccountPage
     */
    protected $linkOwnPagesOnAccountPage = true;
    
    /**
     * Whether users may only see own pages
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $pagePrivateMode
     */
    protected $pagePrivateMode = false;
    
    /**
     * Whether only own entries should be shown on view pages by default or not
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $showOnlyOwnEntries
     */
    protected $showOnlyOwnEntries = false;
    
    /**
     * Whether to allow moderators choosing a user which will be set as creator.
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $allowModerationSpecificCreatorForPage
     */
    protected $allowModerationSpecificCreatorForPage = false;
    
    /**
     * Whether to allow moderators choosing a custom creation date.
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $allowModerationSpecificCreationDateForPage
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
     * @var boolean $showPageHistory
     */
    protected $showPageHistory = true;
    
    
    /**
     * AppSettings constructor.
     *
     * @param VariableApiInterface $variableApi VariableApi service instance
     * @param EntityFactory $entityFactory EntityFactory service instance
     */
    public function __construct(
        VariableApiInterface $variableApi,
        EntityFactory $entityFactory
    ) {
        $this->variableApi = $variableApi;
        $this->entityFactory = $entityFactory;
    
        $this->load();
    }
    
    /**
     * Returns the state of new pages.
     *
     * @return string
     */
    public function getStateOfNewPages()
    {
        return $this->stateOfNewPages;
    }
    
    /**
     * Sets the state of new pages.
     *
     * @param string $stateOfNewPages
     *
     * @return void
     */
    public function setStateOfNewPages($stateOfNewPages)
    {
        if ($this->stateOfNewPages !== $stateOfNewPages) {
            $this->stateOfNewPages = isset($stateOfNewPages) ? $stateOfNewPages : '';
        }
    }
    
    /**
     * Returns the count page views.
     *
     * @return boolean
     */
    public function getCountPageViews()
    {
        return $this->countPageViews;
    }
    
    /**
     * Sets the count page views.
     *
     * @param boolean $countPageViews
     *
     * @return void
     */
    public function setCountPageViews($countPageViews)
    {
        if (boolval($this->countPageViews) !== boolval($countPageViews)) {
            $this->countPageViews = boolval($countPageViews);
        }
    }
    
    /**
     * Returns the google maps api key.
     *
     * @return string
     */
    public function getGoogleMapsApiKey()
    {
        return $this->googleMapsApiKey;
    }
    
    /**
     * Sets the google maps api key.
     *
     * @param string $googleMapsApiKey
     *
     * @return void
     */
    public function setGoogleMapsApiKey($googleMapsApiKey)
    {
        if ($this->googleMapsApiKey !== $googleMapsApiKey) {
            $this->googleMapsApiKey = isset($googleMapsApiKey) ? $googleMapsApiKey : '';
        }
    }
    
    /**
     * Returns the yandex translate api key.
     *
     * @return string
     */
    public function getYandexTranslateApiKey()
    {
        return $this->yandexTranslateApiKey;
    }
    
    /**
     * Sets the yandex translate api key.
     *
     * @param string $yandexTranslateApiKey
     *
     * @return void
     */
    public function setYandexTranslateApiKey($yandexTranslateApiKey)
    {
        if ($this->yandexTranslateApiKey !== $yandexTranslateApiKey) {
            $this->yandexTranslateApiKey = isset($yandexTranslateApiKey) ? $yandexTranslateApiKey : '';
        }
    }
    
    /**
     * Returns the enable raw plugin.
     *
     * @return boolean
     */
    public function getEnableRawPlugin()
    {
        return $this->enableRawPlugin;
    }
    
    /**
     * Sets the enable raw plugin.
     *
     * @param boolean $enableRawPlugin
     *
     * @return void
     */
    public function setEnableRawPlugin($enableRawPlugin)
    {
        if (boolval($this->enableRawPlugin) !== boolval($enableRawPlugin)) {
            $this->enableRawPlugin = boolval($enableRawPlugin);
        }
    }
    
    /**
     * Returns the inherit permissions.
     *
     * @return boolean
     */
    public function getInheritPermissions()
    {
        return $this->inheritPermissions;
    }
    
    /**
     * Sets the inherit permissions.
     *
     * @param boolean $inheritPermissions
     *
     * @return void
     */
    public function setInheritPermissions($inheritPermissions)
    {
        if (boolval($this->inheritPermissions) !== boolval($inheritPermissions)) {
            $this->inheritPermissions = boolval($inheritPermissions);
        }
    }
    
    /**
     * Returns the page styles.
     *
     * @return text
     */
    public function getPageStyles()
    {
        return $this->pageStyles;
    }
    
    /**
     * Sets the page styles.
     *
     * @param text $pageStyles
     *
     * @return void
     */
    public function setPageStyles($pageStyles)
    {
        if ($this->pageStyles !== $pageStyles) {
            $this->pageStyles = isset($pageStyles) ? $pageStyles : '';
        }
    }
    
    /**
     * Returns the section styles.
     *
     * @return text
     */
    public function getSectionStyles()
    {
        return $this->sectionStyles;
    }
    
    /**
     * Sets the section styles.
     *
     * @param text $sectionStyles
     *
     * @return void
     */
    public function setSectionStyles($sectionStyles)
    {
        if ($this->sectionStyles !== $sectionStyles) {
            $this->sectionStyles = isset($sectionStyles) ? $sectionStyles : '';
        }
    }
    
    /**
     * Returns the content styles.
     *
     * @return text
     */
    public function getContentStyles()
    {
        return $this->contentStyles;
    }
    
    /**
     * Sets the content styles.
     *
     * @param text $contentStyles
     *
     * @return void
     */
    public function setContentStyles($contentStyles)
    {
        if ($this->contentStyles !== $contentStyles) {
            $this->contentStyles = isset($contentStyles) ? $contentStyles : '';
        }
    }
    
    /**
     * Returns the enable optional string 1.
     *
     * @return boolean
     */
    public function getEnableOptionalString1()
    {
        return $this->enableOptionalString1;
    }
    
    /**
     * Sets the enable optional string 1.
     *
     * @param boolean $enableOptionalString1
     *
     * @return void
     */
    public function setEnableOptionalString1($enableOptionalString1)
    {
        if (boolval($this->enableOptionalString1) !== boolval($enableOptionalString1)) {
            $this->enableOptionalString1 = boolval($enableOptionalString1);
        }
    }
    
    /**
     * Returns the enable optional string 2.
     *
     * @return boolean
     */
    public function getEnableOptionalString2()
    {
        return $this->enableOptionalString2;
    }
    
    /**
     * Sets the enable optional string 2.
     *
     * @param boolean $enableOptionalString2
     *
     * @return void
     */
    public function setEnableOptionalString2($enableOptionalString2)
    {
        if (boolval($this->enableOptionalString2) !== boolval($enableOptionalString2)) {
            $this->enableOptionalString2 = boolval($enableOptionalString2);
        }
    }
    
    /**
     * Returns the enable optional text.
     *
     * @return boolean
     */
    public function getEnableOptionalText()
    {
        return $this->enableOptionalText;
    }
    
    /**
     * Sets the enable optional text.
     *
     * @param boolean $enableOptionalText
     *
     * @return void
     */
    public function setEnableOptionalText($enableOptionalText)
    {
        if (boolval($this->enableOptionalText) !== boolval($enableOptionalText)) {
            $this->enableOptionalText = boolval($enableOptionalText);
        }
    }
    
    /**
     * Returns the ignore bundle name in routes.
     *
     * @return boolean
     */
    public function getIgnoreBundleNameInRoutes()
    {
        return $this->ignoreBundleNameInRoutes;
    }
    
    /**
     * Sets the ignore bundle name in routes.
     *
     * @param boolean $ignoreBundleNameInRoutes
     *
     * @return void
     */
    public function setIgnoreBundleNameInRoutes($ignoreBundleNameInRoutes)
    {
        if (boolval($this->ignoreBundleNameInRoutes) !== boolval($ignoreBundleNameInRoutes)) {
            $this->ignoreBundleNameInRoutes = boolval($ignoreBundleNameInRoutes);
        }
    }
    
    /**
     * Returns the ignore entity name in routes.
     *
     * @return boolean
     */
    public function getIgnoreEntityNameInRoutes()
    {
        return $this->ignoreEntityNameInRoutes;
    }
    
    /**
     * Sets the ignore entity name in routes.
     *
     * @param boolean $ignoreEntityNameInRoutes
     *
     * @return void
     */
    public function setIgnoreEntityNameInRoutes($ignoreEntityNameInRoutes)
    {
        if (boolval($this->ignoreEntityNameInRoutes) !== boolval($ignoreEntityNameInRoutes)) {
            $this->ignoreEntityNameInRoutes = boolval($ignoreEntityNameInRoutes);
        }
    }
    
    /**
     * Returns the ignore first tree level in routes.
     *
     * @return boolean
     */
    public function getIgnoreFirstTreeLevelInRoutes()
    {
        return $this->ignoreFirstTreeLevelInRoutes;
    }
    
    /**
     * Sets the ignore first tree level in routes.
     *
     * @param boolean $ignoreFirstTreeLevelInRoutes
     *
     * @return void
     */
    public function setIgnoreFirstTreeLevelInRoutes($ignoreFirstTreeLevelInRoutes)
    {
        if (boolval($this->ignoreFirstTreeLevelInRoutes) !== boolval($ignoreFirstTreeLevelInRoutes)) {
            $this->ignoreFirstTreeLevelInRoutes = boolval($ignoreFirstTreeLevelInRoutes);
        }
    }
    
    /**
     * Returns the permalink suffix.
     *
     * @return string
     */
    public function getPermalinkSuffix()
    {
        return $this->permalinkSuffix;
    }
    
    /**
     * Sets the permalink suffix.
     *
     * @param string $permalinkSuffix
     *
     * @return void
     */
    public function setPermalinkSuffix($permalinkSuffix)
    {
        if ($this->permalinkSuffix !== $permalinkSuffix) {
            $this->permalinkSuffix = isset($permalinkSuffix) ? $permalinkSuffix : '';
        }
    }
    
    /**
     * Returns the page entries per page.
     *
     * @return integer
     */
    public function getPageEntriesPerPage()
    {
        return $this->pageEntriesPerPage;
    }
    
    /**
     * Sets the page entries per page.
     *
     * @param integer $pageEntriesPerPage
     *
     * @return void
     */
    public function setPageEntriesPerPage($pageEntriesPerPage)
    {
        if (intval($this->pageEntriesPerPage) !== intval($pageEntriesPerPage)) {
            $this->pageEntriesPerPage = intval($pageEntriesPerPage);
        }
    }
    
    /**
     * Returns the link own pages on account page.
     *
     * @return boolean
     */
    public function getLinkOwnPagesOnAccountPage()
    {
        return $this->linkOwnPagesOnAccountPage;
    }
    
    /**
     * Sets the link own pages on account page.
     *
     * @param boolean $linkOwnPagesOnAccountPage
     *
     * @return void
     */
    public function setLinkOwnPagesOnAccountPage($linkOwnPagesOnAccountPage)
    {
        if (boolval($this->linkOwnPagesOnAccountPage) !== boolval($linkOwnPagesOnAccountPage)) {
            $this->linkOwnPagesOnAccountPage = boolval($linkOwnPagesOnAccountPage);
        }
    }
    
    /**
     * Returns the page private mode.
     *
     * @return boolean
     */
    public function getPagePrivateMode()
    {
        return $this->pagePrivateMode;
    }
    
    /**
     * Sets the page private mode.
     *
     * @param boolean $pagePrivateMode
     *
     * @return void
     */
    public function setPagePrivateMode($pagePrivateMode)
    {
        if (boolval($this->pagePrivateMode) !== boolval($pagePrivateMode)) {
            $this->pagePrivateMode = boolval($pagePrivateMode);
        }
    }
    
    /**
     * Returns the show only own entries.
     *
     * @return boolean
     */
    public function getShowOnlyOwnEntries()
    {
        return $this->showOnlyOwnEntries;
    }
    
    /**
     * Sets the show only own entries.
     *
     * @param boolean $showOnlyOwnEntries
     *
     * @return void
     */
    public function setShowOnlyOwnEntries($showOnlyOwnEntries)
    {
        if (boolval($this->showOnlyOwnEntries) !== boolval($showOnlyOwnEntries)) {
            $this->showOnlyOwnEntries = boolval($showOnlyOwnEntries);
        }
    }
    
    /**
     * Returns the allow moderation specific creator for page.
     *
     * @return boolean
     */
    public function getAllowModerationSpecificCreatorForPage()
    {
        return $this->allowModerationSpecificCreatorForPage;
    }
    
    /**
     * Sets the allow moderation specific creator for page.
     *
     * @param boolean $allowModerationSpecificCreatorForPage
     *
     * @return void
     */
    public function setAllowModerationSpecificCreatorForPage($allowModerationSpecificCreatorForPage)
    {
        if (boolval($this->allowModerationSpecificCreatorForPage) !== boolval($allowModerationSpecificCreatorForPage)) {
            $this->allowModerationSpecificCreatorForPage = boolval($allowModerationSpecificCreatorForPage);
        }
    }
    
    /**
     * Returns the allow moderation specific creation date for page.
     *
     * @return boolean
     */
    public function getAllowModerationSpecificCreationDateForPage()
    {
        return $this->allowModerationSpecificCreationDateForPage;
    }
    
    /**
     * Sets the allow moderation specific creation date for page.
     *
     * @param boolean $allowModerationSpecificCreationDateForPage
     *
     * @return void
     */
    public function setAllowModerationSpecificCreationDateForPage($allowModerationSpecificCreationDateForPage)
    {
        if (boolval($this->allowModerationSpecificCreationDateForPage) !== boolval($allowModerationSpecificCreationDateForPage)) {
            $this->allowModerationSpecificCreationDateForPage = boolval($allowModerationSpecificCreationDateForPage);
        }
    }
    
    /**
     * Returns the enabled finder types.
     *
     * @return string
     */
    public function getEnabledFinderTypes()
    {
        return $this->enabledFinderTypes;
    }
    
    /**
     * Sets the enabled finder types.
     *
     * @param string $enabledFinderTypes
     *
     * @return void
     */
    public function setEnabledFinderTypes($enabledFinderTypes)
    {
        if ($this->enabledFinderTypes !== $enabledFinderTypes) {
            $this->enabledFinderTypes = isset($enabledFinderTypes) ? $enabledFinderTypes : '';
        }
    }
    
    /**
     * Returns the revision handling for page.
     *
     * @return string
     */
    public function getRevisionHandlingForPage()
    {
        return $this->revisionHandlingForPage;
    }
    
    /**
     * Sets the revision handling for page.
     *
     * @param string $revisionHandlingForPage
     *
     * @return void
     */
    public function setRevisionHandlingForPage($revisionHandlingForPage)
    {
        if ($this->revisionHandlingForPage !== $revisionHandlingForPage) {
            $this->revisionHandlingForPage = isset($revisionHandlingForPage) ? $revisionHandlingForPage : '';
        }
    }
    
    /**
     * Returns the maximum amount of page revisions.
     *
     * @return string
     */
    public function getMaximumAmountOfPageRevisions()
    {
        return $this->maximumAmountOfPageRevisions;
    }
    
    /**
     * Sets the maximum amount of page revisions.
     *
     * @param string $maximumAmountOfPageRevisions
     *
     * @return void
     */
    public function setMaximumAmountOfPageRevisions($maximumAmountOfPageRevisions)
    {
        if ($this->maximumAmountOfPageRevisions !== $maximumAmountOfPageRevisions) {
            $this->maximumAmountOfPageRevisions = isset($maximumAmountOfPageRevisions) ? $maximumAmountOfPageRevisions : '';
        }
    }
    
    /**
     * Returns the period for page revisions.
     *
     * @return string
     */
    public function getPeriodForPageRevisions()
    {
        return $this->periodForPageRevisions;
    }
    
    /**
     * Sets the period for page revisions.
     *
     * @param string $periodForPageRevisions
     *
     * @return void
     */
    public function setPeriodForPageRevisions($periodForPageRevisions)
    {
        if ($this->periodForPageRevisions !== $periodForPageRevisions) {
            $this->periodForPageRevisions = isset($periodForPageRevisions) ? $periodForPageRevisions : '';
        }
    }
    
    /**
     * Returns the show page history.
     *
     * @return boolean
     */
    public function getShowPageHistory()
    {
        return $this->showPageHistory;
    }
    
    /**
     * Sets the show page history.
     *
     * @param boolean $showPageHistory
     *
     * @return void
     */
    public function setShowPageHistory($showPageHistory)
    {
        if (boolval($this->showPageHistory) !== boolval($showPageHistory)) {
            $this->showPageHistory = boolval($showPageHistory);
        }
    }
    
    
    /**
     * Loads module variables from the database.
     */
    protected function load()
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
    public function save()
    {
        $this->variableApi->set('ZikulaContentModule', 'stateOfNewPages', $this->getStateOfNewPages());
        $this->variableApi->set('ZikulaContentModule', 'countPageViews', $this->getCountPageViews());
        $this->variableApi->set('ZikulaContentModule', 'googleMapsApiKey', $this->getGoogleMapsApiKey());
        $this->variableApi->set('ZikulaContentModule', 'yandexTranslateApiKey', $this->getYandexTranslateApiKey());
        $this->variableApi->set('ZikulaContentModule', 'enableRawPlugin', $this->getEnableRawPlugin());
        $this->variableApi->set('ZikulaContentModule', 'inheritPermissions', $this->getInheritPermissions());
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
    
        $entityManager = $this->entityFactory->getObjectManager();
        $revisionHandling = $this->getRevisionHandlingForPage();
        $limitParameter = '';
        if ('limitedByAmount' == $revisionHandling) {
            $limitParameter = $this->getMaximumAmountOfPageRevisions();
        } elseif ('limitedByDate' == $revisionHandling) {
            $limitParameter = $this->getPeriodForPageRevisions();
        }
    
        $logEntriesRepository = $entityManager->getRepository('ZikulaContentModule:PageLogEntryEntity');
        $logEntriesRepository->purgeHistory($revisionHandling, $limitParameter);
    }
}
