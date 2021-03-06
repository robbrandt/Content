<?php

/**
 * Content
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://github.com/zikula-modules/Content
 * @license See license.txt
 */
class Content_Api_Content extends Zikula_AbstractApi
{
    /* =[ Standard CRUD methods ]===================================================== */

    public function getContent($args)
    {
        $id = (int) $args['id'];
        $language = (array_key_exists('language', $args) ? $args['language'] : ZLanguage::getLanguageCode());
        $translate = (array_key_exists('translate', $args) ? $args['translate'] : true);
        $view = isset($args['view']) ? $args['view'] : null;

        $content = $this->contentGetContent('content', $id, true, $language, $translate, null, $view);
        if ($content === false) {
            return false;
        }
        if (count($content) == 0) {
            return LogUtil::registerError($this->__("Error! Unknown content-ID"));
        }

        return $content[0];
    }

    public function getPageContent($args)
    {
        $pageId = (int) $args['pageId'];
        $editing = (array_key_exists('editing', $args) ? $args['editing'] : false);
        $language = (array_key_exists('language', $args) ? $args['language'] : ZLanguage::getLanguageCode());
        $expandContent = (array_key_exists('expandContent', $args) ? $args['expandContent'] : true);
        $translate = (array_key_exists('translate', $args) ? $args['translate'] : true);

        $contentList = $this->contentGetContent('page', $pageId, $editing, $language, $translate);

        $content = array();
        foreach ($contentList as $c) {
            if (isset($c['plugin'])) {
                $c['title'] = $c['plugin']->getTitle();
                $c['isTranslatable'] = $c['plugin']->isTranslatable(); // dup line 127?
                $output = '';
                if ($expandContent) {
                    $output = $c['plugin']->displayStart();
                    if ($editing) {
                        $output .= $c['plugin']->displayEditing();
                    } else {
                        $output .= $c['plugin']->display();
                    }
                    $output .= $c['plugin']->displayEnd();
                }
                $c['output'] = $output;
                $c['plugin']->destroyView(); // save memory
            } else {
                $c['title'] = $this->__('disabled plugin');
                $c['isTranslatable'] = false;
                $c['output'] = $this->__f('Disabled: Inaccessible plugin output (%1$s, %2$s).', array($c['module'], $c['type']));
            }
            $content[$c['areaIndex']][] = $c;
        }

        return $content;
    }

    public function GetSimplePageContent($args)
    {
        $pageId = (int) $args['pageId'];

        $table = DBUtil::getTables();
        $contentTable = $table['content_content'];
        $contentColumn = $table['content_content_column'];

        $where = "$contentColumn[pageId] = $pageId";
        $content = DBUtil::selectObjectArray('content_content', $where);

        return $content;
    }

    protected function contentGetContent($mode, $id, $editing, $language, $translate, $orderBy = null, $view = null)
    {
        $id = (int) $id;
        $language = DataUtil::formatForStore($language);

        $table = DBUtil::getTables();
        $contentTable = $table['content_content'];
        $contentColumn = $table['content_content_column'];
        $translatedTable = $table['content_translatedcontent'];
        $translatedColumn = $table['content_translatedcontent_column'];

        if ($mode == 'content') {
            $restriction = "$contentColumn[id] = $id";
        } else {
            $restriction = "$contentColumn[pageId] = $id";
        }
        if (!$editing) {
            $restriction .= " and c.$contentColumn[active] = 1 and c.$contentColumn[visiblefor] " . (UserUtil::isLoggedIn() ? '<=1' : '>=1');
        }

        if (empty($orderBy)) {
            $orderBy = "$contentColumn[areaIndex], $contentColumn[position]";
        }

        $cols = DBUtil::_getAllColumns('content_content');
        $ca = DBUtil::getColumnsArray('content_content');
        $ca[] = 'translated';

        $sql = "
            SELECT $cols, $translatedColumn[data] AS translated
            FROM $contentTable c
            LEFT JOIN $translatedTable t
            ON t.$translatedColumn[contentId] = $contentColumn[id]
            AND t.$translatedColumn[language] = '$language'
            WHERE $restriction
            ORDER BY $orderBy";
        
        $dbresult = DBUtil::executeSQL($sql);
        $content = DBUtil::marshallObjects($dbresult, $ca);

        for ($i = 0, $cou = count($content); $i < $cou; ++$i) {
            $c = &$content[$i];
            $c['data'] = (empty($c['data']) ? null : unserialize($c['data']));
            $c['translated'] = (empty($c['translated']) ? null : unserialize($c['translated']));

            if ($translate) {
                if (is_array($c['translated']) && is_array($c['data'])) {
                    $c['data'] = array_merge($c['data'], $c['translated']);
                }
            }

            $contentPlugin = $this->getContentPlugin($c, $view);
            if ($contentPlugin) {
                $content[$i]['plugin'] = $contentPlugin;
                $content[$i]['isTranslatable'] = $contentPlugin->isTranslatable(); // dup line 44?
            } else {
                $content[$i]['isTranslatable'] = false;
            }
        }

        return $content;
    }

    public function getPageAndSubPageContent($args)
    {
        $pageId = (int) $args['pageId'];

        $table = DBUtil::getTables();
        $pageTable = $table['content_page'];
        $pageColumn = $table['content_page_column'];
        $contentTable = $table['content_content'];
        $contentColumn = $table['content_content_column'];

        // Fetch all content items that belongs to page X or any of it's sub pages
        $sql = "
            SELECT co.*
            FROM $pageTable page
            JOIN $pageTable subPage
            ON subPage.$pageColumn[setLeft] >= page.$pageColumn[setLeft]
            AND subPage.$pageColumn[setRight] <= page.$pageColumn[setRight]
            JOIN $contentTable co
            ON co.$contentColumn[pageId] = subPage.$pageColumn[id]
            WHERE page.$pageColumn[id] = $pageId";

        $dbresult = DBUtil::executeSQL($sql);

        $ca = DBUtil::getColumnsArray('content_content');
        $content = DBUtil::marshallObjects($dbresult, $ca);

        for ($i = 0, $cou = count($content); $i < $cou; ++$i) {
            $c = &$content[$i];
            $c['data'] = (empty($c['data']) ? null : unserialize($c['data']));
            $contentPlugin = $this->getContentPlugin($c);
            if ($contentPlugin === false) {
                return LogUtil::registerError($this->__("Error! Can't load content plugin"));
            }
            $content[$i]['plugin'] = $contentPlugin;
        }

        return $content;
    }

    /* =[ Create new content element ]================================================ */

    public function newContent($args)
    {
        $contentData = $args['content'];
        $pageId = (int) $args['pageId'];
        $contentAreaIndex = (int) $args['contentAreaIndex'];
        $position = (int) $args['position'];
        $addVersion = isset($args['addVersion']) ? $args['addVersion'] : true;

        if (!$this->contentMoveContentDown($position, $contentAreaIndex, $pageId)) {
            return false;
        }

        $classname = $contentData['module'] . "_ContentType_" . $contentData['type'];
        $view = Zikula_View::getInstance($contentData['module']);
        $contentPlugin = new $classname($view);

        $page = ModUtil::apiFunc('Content', 'Page', 'getPage', array(
            'id' => $pageId,
            'includeContent' => false,
            'filter' => array('checkActive' => false)));
        $contentPlugin->setPageCategoryId($page['categoryId']);

        $contentData['pageId'] = $pageId;
        $contentData['areaIndex'] = $contentAreaIndex;
        $contentData['position'] = $position;
        if (!isset($contentData['data'])) {
            $contentData['data'] = serialize($contentPlugin->getDefaultData());
        } else {
            $contentData['data'] = serialize($contentData['data']);
        }

        DBUtil::insertObject($contentData, 'content_content', 'id', true); // true => preserve values (id-column)

        if ($addVersion) {
            $ok = ModUtil::apiFunc('Content', 'History', 'addPageVersion', array('pageId' => $pageId, 'action' => '_CONTENT_HISTORYCONTENTADDED' /* delayed translation */));
            if ($ok === false) {
                return false;
            }
        }

        Content_Util::clearCache();

        return $contentData['id'];
    }

    protected function contentGetLastContentPosition($pageId, $contentAreaIndex)
    {
        $pageId = (int) $pageId;
        $contentAreaIndex = (int) $contentAreaIndex;

        $table = DBUtil::getTables();
        $contentTable = $table['content_content'];
        $contentColumn = $table['content_content_column'];

        $sql = "
            SELECT MAX($contentColumn[position])
            FROM $contentTable
            WHERE $contentColumn[pageId] = $pageId";

        $pos = DBUtil::selectScalar($sql);

        return $pos === null ? -1 : (int) $pos;
    }

    /* =[ Clone content element on same page ]==================================================== */

    // TODO: maybe reuse in/with copyContentOfPageToPage
    public function cloneContent($args)
    {
        $contentId = (int) $args['id'];
        $cloneTranslation = isset($newPage['translation']) ? $newPage['translation'] : true;
        $addVersion = isset($args['addVersion']) ? $args['addVersion'] : true;

        $contentData = DBUtil::selectObjectByID('content_content', $contentId);
        if ($contentData === false) {
            return false;
        }

        $contentData['position']++;
        unset($contentData['id']);

        if (!$this->contentMoveContentDown($contentData['position'], $contentData['areaIndex'], $contentData['pageId'])) {
            return false;
        }

        DBUtil::insertObject($contentData, 'content_content');
        $newContentId = $contentData['id'];

        $this->cloneContentAdditions($contentId, $newContentId, $cloneTranslation);

        if ($addVersion) {
            $ok = ModUtil::apiFunc('Content', 'History', 'addPageVersion', array('pageId' => $pageId, 'action' => '_CONTENT_HISTORYCONTENTADDED' /* delayed translation */));
            if ($ok === false) {
                return false;
            }
        }

        Content_Util::clearCache();

        return $newContentId;
    }

    private function cloneContentAdditions($oldContentId, $newContentId, $cloneTranslation)
    {
        $currentLanguage = ZLanguage::getLanguageCode();
        $dbtables = DBUtil::getTables();

        $contentSearchColumn = $dbtables['content_searchable_column'];

        $where = $contentSearchColumn['contentId'] . ' = ' . $oldContentId . ' AND ' . $contentSearchColumn['language'] . ' IN (\'' . DataUtil::formatForStore($currentLanguage) . '\', \'\')';
        $searchableData = DBUtil::selectObjectArray('content_searchable', $where);
        if (count($searchableData) > 0) {
            foreach ($searchableData as &$s) {
                $s['contentId'] = $newContentId;
            }
            DBUtil::insertObjectArray($searchableData, 'content_searchable', 'searchableId');
        }

        if (!$cloneTranslation) {
            return;
        }

        $translatedColumn = $dbtables['content_translatedcontent_column'];
        $translations = DBUtil::selectObjectArray('content_translatedcontent', "$translatedColumn[contentId] = $oldContentId");

        if (count($translations) < 1) {
            return;
        }

        foreach ($translations as &$t) {
            $t['contentId'] = $newContentId;
        }
        DBUtil::insertObjectArray($translations, 'content_translatedcontent', 'contentId', true);

        $where = $contentSearchColumn['contentId'] . ' = ' . $oldContentId . ' AND ' . $contentSearchColumn['language'] . ' NOT IN (\'' . DataUtil::formatForStore($currentLanguage) . '\', \'\')';
        $searchableData = DBUtil::selectObjectArray('content_searchable', $where);

        if (count($searchableData) > 0) {
            foreach ($searchableData as &$s) {
                $s['contentId'] = $newContentId;
            }
            DBUtil::insertObjectArray($searchableData, 'content_searchable', 'searchableId', true);
        }
    }

    /* =[ Update content element ]==================================================== */

    public function updateContent($args)
    {
        $contentData = $args['content'];
        $addVersion = isset($args['addVersion']) ? $args['addVersion'] : true;

        $contentData['id'] = $args['id'];
        if (isset($contentData['data'])) {
            $contentData['data'] = serialize($contentData['data']);
        }

        DBUtil::updateObject($contentData, 'content_content');

        if (isset($args['searchableText']) && !empty($args['searchableText'])) {
            if (!$this->contentUpdateSearchableText((int) $args['id'], $args['searchableText'])) {
                return false;
            }
        }

        $content = $this->getContent(array('id' => $contentData['id']));
        if ($content === false) {
            return false;
        }
        if ($addVersion) {
            $ok = ModUtil::apiFunc('Content', 'History', 'addPageVersion', array('pageId' => $content['pageId'], 'action' => '_CONTENT_HISTORYCONTENTUPDATED' /* delayed translation */));
            if ($ok === false) {
                return false;
            }
        }

        Content_Util::clearCache();

        return true;
    }

    /**
     * Update the status of the item
     *
     * @param int contentId
     * @param string active (optional)
     * @param string inMenu (optional)
     *
     * @return true
     */
    public function updateState($args)
    {
        // Argument check
        if (!isset($args['contentId'])) {
            return LogUtil::registerArgsError();
        }

        $content = array('id' => $args['contentId']);
        if (isset($args['active'])) {
            $content['active'] = ($args['active'] == 'true') ? 0 : 1;
        }

        DBUtil::updateObject($content, 'content_content');

        return true;
    }

    protected function contentUpdateSearchableText($contentId, $text, $language = '')
    {
        $languages = array();
        if ($language !== '') {
            $languages[] = $language;
        } else {
            $languages[] = ZLanguage::getLanguageCode();
            $languages[] = '';
        }

        // We delete first then insert, since it may not already exist.
        $dbtables = DBUtil::getTables();
        $contentSearchColumn = $dbtables['content_searchable_column'];
        $where = $contentSearchColumn['contentId'] . ' = ' . $contentId . ' AND ' . $contentSearchColumn['language'] . ' IN (\'' . implode('\', \'', $languages) . '\')';
        DBUtil::deleteWhere('content_searchable', $where);

        $searchObj = array('contentId' => $contentId, 'text' => $text, 'language' => $languages[0]);
        DBUtil::insertObject($searchObj, 'content_searchable', 'searchableId', true);

        return true;
    }

    /* =[ Copy content ]==================================================== */

    public function copyContentOfPageToPage($args)
    {
        $fromPage = (int) $args['fromPageId'];
        $toPage = (int) $args['toPageId'];
        if ($fromPage <= 0 || $toPage <= 0) {
            return false;
        }
        $cloneTranslation = isset($args['cloneTranslation']) ? $args['cloneTranslation'] : true;

        $content = $this->GetSimplePageContent(array('pageId' => $fromPage));
        for ($i = 0; $i < count($content); $i++) {
            $contentData = $content[$i];
            $contentData['id'] = null;
            $contentData['pageId'] = $toPage;
            DBUtil::insertObject($contentData, 'content_content', 'id');
            $id = $contentData['id']; // ID of object we just inserted

            $this->cloneContentAdditions($id, $cloneTranslation);
        }
        Content_Util::clearCache();

        return true;
    }

    /* =[ Delete content element ]==================================================== */

    public function deleteContent($args)
    {
        $contentId = (int) $args['contentId'];
        $addVersion = isset($args['addVersion']) ? $args['addVersion'] : true;

        $content = $this->getContent(array('id' => $contentId));
        if ($content === false) {
            return false;
        }
        $contentType = $this->getContentType($content);
        if ($contentType !== false) {
            $contentType['plugin']->delete();
        } else {
            // Plugin probably disabled, plugin delete will not work
        }

        if (!$this->contentRemoveContent($contentId)) {
            return false;
        }
        DBUtil::deleteObjectByID('content_content', $contentId);

        $dbtables = DBUtil::getTables();
        $contentSearchColumn = $dbtables['content_searchable_column'];
        $where = $contentSearchColumn['contentId'] . ' = ' . $contentId;
        DBUtil::deleteWhere('content_searchable', $where);

        $ok = $this->deleteTranslation(array('contentId' => $contentId, 'includeHistory' => false));
        if ($ok === false) {
            return false;
        }
        if ($addVersion) {
            $ok = ModUtil::apiFunc('Content', 'History', 'addPageVersion', array('pageId' => $content['pageId'], 'action' => '_CONTENT_HISTORYCONTENTDELETED' /* delayed translation */));
            if ($ok === false) {
                return false;
            }
        }

        Content_Util::clearCache();

        return true;
    }

    public function deletePageAndSubPageContent($args)
    {
        $pageId = (int) $args['pageId'];

        // Get all content items on this page and all it's sub pages
        $contentItems = $this->getPageAndSubPageContent(array('pageId' => $pageId));
        if ($contentItems === false) {
            return false;
        }
        for ($i = 0, $cou = count($contentItems); $i < $cou; ++$i) {
            // Make sure content items get a chance to delete themselves
            $contentItems[$i]['plugin']->delete();

            // Delete from DB
            DBUtil::deleteObjectByID('content_content', $contentItems[$i]['id']);
        }

        Content_Util::clearCache();

        return true;
    }

    /* =[ Translate content ]========================================================= */

    public function updateTranslation($args)
    {
        $contentId = (int) $args['contentId'];
        $language = DataUtil::formatForStore($args['language']);
        $translated = $args['translated'];
        $addVersion = isset($args['addVersion']) ? $args['addVersion'] : true;

        // Delete optional existing translation
        $dbtables = DBUtil::getTables();
        $translatedColumn = $dbtables['content_translatedcontent_column'];
        $where = $translatedColumn['contentId'] . ' = \'' . DataUtil::formatForStore($contentId) . '\' AND ' . $translatedColumn['language'] . ' = \'' . DataUtil::formatForStore($language) . '\'';
        DBUtil::deleteObject(array(), 'content_translatedcontent', $where, 'contentId');

        $translatedData = array('contentId' => $contentId, 'language' => $language);

        // Insert new
        $translatedData['data'] = serialize($translated);
        DBUtil::insertObject($translatedData, 'content_translatedcontent');

        $content = $this->getContent(array('id' => $contentId));
        if ($content === false) {
            return false;
        }

        if (isset($args['searchableText']) && !empty($args['searchableText'])) {
            if (!$this->contentUpdateSearchableText($contentId, $args['searchableText'], $language)) {
                return false;
            }
        }

        if ($addVersion) {
            $ok = ModUtil::apiFunc('Content', 'History', 'addPageVersion', array('pageId' => $content['pageId'], 'action' => '_CONTENT_HISTORYTRANSLATED' /* delayed translation */));
            if ($ok === false) {
                return false;
            }
        }

        Content_Util::clearCache();

        return true;
    }

    public function deleteTranslation($args)
    {
        $contentId = (int) $args['contentId'];
        $language = isset($args['language']) ? $args['language'] : null;
        $includeHistory = isset($args['includeHistory']) ? $args['includeHistory'] : true;

        $translatedData = array('contentId' => $contentId);
        if ($language !== null) {
            $translatedData['language'] = $language;
        }
        DBUtil::deleteObject($translatedData, 'content_translatedcontent', '', 'contentId');

        $searchableLanguage = ($language !== null) ? $language : ZLanguage::getLanguageCode();
        $dbtables = DBUtil::getTables();
        $contentSearchColumn = $dbtables['content_searchable_column'];
        $where = $contentSearchColumn['contentId'] . ' = ' . $contentId . ' AND ' . $contentSearchColumn['language'] . ' = \'' . DataUtil::formatForStore($searchableLanguage) . '\'';
        DBUtil::deleteWhere('content_searchable', $where);

        // Get content to find page ID
        if ($includeHistory) {
            $content = $this->getContent(array('id' => $contentId));
            if ($content === false) {
                return false;
            }
            $ok = ModUtil::apiFunc('Content', 'History', 'addPageVersion', array('pageId' => $content['pageId'], 'action' => '_CONTENT_HISTORYTRANSLATIONDEL' /* delayed translation */));
            if ($ok === false) {
                return false;
            }
        }

        Content_Util::clearCache();

        return true;
    }

    public function deletePageTranslations($args)
    {
        $pageId = (int) $args['pageId'];
        $language = isset($args['language']) ? $args['language'] : null;

        $table = DBUtil::getTables();
        $contentTable = $table['content_content'];
        $contentColumn = $table['content_content_column'];
        $translatedTable = $table['content_translatedcontent'];
        $translatedColumn = $table['content_translatedcontent_column'];

        if ($language != null) {
            $restriction = "AND t.$translatedColumn[language] = '" . DataUtil::formatForStore($language) . "'";
        } else {
            $restriction = '';
        }
        $sql = "
            DELETE t
            FROM $translatedTable t, $contentTable c
            WHERE t.$translatedColumn[contentId] = c.$contentColumn[id]
            $restriction
            AND c.$contentColumn[pageId] = $pageId";

        $dbresult = DBUtil::executeSQL($sql);

        Content_Util::clearCache();

        return true;
    }

    public function getTranslationInfo($args)
    {
        $contentId = (isset($args['contentId']) ? (int) $args['contentId'] : null);
        $pageId = (isset($args['pageId']) ? (int) $args['pageId'] : null);

        // fetch content + page info

        if ($contentId != null) {
            $contentItem = $this->getContent(array('id' => $contentId));
            if ($contentItem === false) {
                return false;
            }
            $pageId = $contentItem['pageId'];
        }

        $page = ModUtil::apiFunc('Content', 'Page', 'getPage', array('id' => $pageId));
        if ($page === false) {
            return false;
        }
        $layout = ModUtil::apiFunc('Content', 'Layout', 'getLayoutPlugin', array(
            'layout' => $page['layout']));
        if ($layout === false) {
            return false;
        }
        $contentItems = $this->contentGetContent('page', $pageId, false, null, false);
        if ($contentItems === false) {
            return false;
        }
        $translatableItems = array();
        foreach ($contentItems as $item) {
            if (isset($item['plugin']) && $item['plugin']->isTranslatable()) {
                $translatableItems[] = $item;
            }
        }

        $translationItems = array();
        $i = 1;
        $count = count($translatableItems);
        $currentIndex = -1;
        foreach ($translatableItems as $item) {
            if ($item['plugin']->isTranslatable()) {
                $translationItems[] = array('text' => $layout->getContentAreaTitle($item['areaIndex']) . ": $item[type] ($i/$count)", 'value' => $item['id']);
                if ($item['id'] == $contentId) {
                    $currentIndex = $i - 1;
                }
                ++$i;
            }
        }

        $nextContentId = null;
        $prevContentId = null;

        if ($contentId != null) {
            if ($currentIndex < count($translationItems) - 1) {
                $nextContentId = $translatableItems[$currentIndex + 1]['id'];
            }
            if ($currentIndex > 0) {
                $prevContentId = $translatableItems[$currentIndex - 1]['id'];
            }
        } else {
            if (count($translatableItems) > 0) {
                $nextContentId = $translatableItems[0]['id'];
            }
        }
        $curContentId = $translatableItems[$currentIndex]['id'];

        return array('items' => $translationItems, 'curContentId' => $curContentId, 'nextContentId' => $nextContentId, 'prevContentId' => $prevContentId);
    }

    public function getTranslations($args)
    {
        $pageId = (int) $args['pageId'];

        $table = DBUtil::getTables();
        $translatedTable = $table['content_translatedcontent'];
        $translatedColumn = $table['content_translatedcontent_column'];
        $contentTable = $table['content_content'];
        $contentColumn = $table['content_content_column'];

        $cols = DBUtil::_getAllColumns('content_translatedcontent');
        $ca = DBUtil::getColumnsArray('content_translatedcontent');

        $sql = "
            SELECT $cols
            FROM $translatedTable t
            LEFT JOIN $contentTable c
            ON c.$contentColumn[id] = t.$translatedColumn[contentId]
            WHERE c.$contentColumn[pageId] = $pageId";

        $dbresult = DBUtil::executeSQL($sql);
        $translations = DBUtil::marshallObjects($dbresult, $ca);

        return $translations;
    }

    /* =[ Moving content ]============================================================ */

    public function dragContent($args)
    {
        if (!isset($args['pageId']) || !isset($args['contentId']) || !isset($args['contentAreaIndex']) || !isset($args['position'])) {
            return LogUtil::registerArgsError();
        }

        $pageId = (int) $args['pageId'];
        $contentId = (int) $args['contentId'];
        $contentAreaIndex = (int) $args['contentAreaIndex'];
        $position = (int) $args['position'];

        // This will remove the content item from the content Area, but no deletion
        if (!$this->contentRemoveContent($contentId)) {
            return false;
        }
        // Insert the removed content item into the new location
        if (!$this->contentInsertContent($contentId, $position, $contentAreaIndex, $pageId)) {
            return false;
        }
        $ok = ModUtil::apiFunc('Content', 'History', 'addPageVersion', array('pageId' => $pageId, 'action' => '_CONTENT_HISTORYCONTENTMOVED' /* delayed translation */));
        if ($ok === false) {
            return false;
        }

        Content_Util::clearCache();

        return true;
    }

    // Remove content from content area, but do not delete it
    protected function contentRemoveContent($contentId)
    {
        $contentData = $this->getContent(array('id' => $contentId));
        if ($contentData === false) {
            return false;
        }
        $pageId = (int) $contentData['pageId'];
        $contentAreaIndex = (int) $contentData['areaIndex'];
        $position = (int) $contentData['position'];

        $table = DBUtil::getTables();
        $contentTable = $table['content_content'];
        $contentColumn = $table['content_content_column'];

        $sql = "
            UPDATE $contentTable
            SET $contentColumn[position] = $contentColumn[position]-1
            WHERE $contentColumn[pageId] = $pageId
            AND $contentColumn[areaIndex] = $contentAreaIndex
            AND $contentColumn[position] > $position";

        DBUtil::executeSQL($sql);

        Content_Util::clearCache();

        return true;
    }

    // Insert content in content area
    protected function contentInsertContent($contentId, $position, $contentAreaIndex, $pageId)
    {
        $contentData = $this->getContent(array('id' => $contentId));
        if ($contentData === false) {
            return false;
        }
        if (!$this->contentMoveContentDown($position, $contentAreaIndex, $pageId)) {
            return false;
        }
        $table = DBUtil::getTables();
        $contentTable = $table['content_content'];
        $contentColumn = $table['content_content_column'];

        $contentData = array('id' => $contentId, 'position' => $position, 'areaIndex' => $contentAreaIndex);
        DBUtil::updateObject($contentData, 'content_content');

        Content_Util::clearCache();

        return true;
    }

    protected function contentMoveContentDown($position, $contentAreaIndex, $pageId)
    {
        $table = DBUtil::getTables();
        $contentTable = $table['content_content'];
        $contentColumn = $table['content_content_column'];

        $sql = "
            UPDATE $contentTable
            SET $contentColumn[position] = $contentColumn[position]+1
            WHERE $contentColumn[pageId] = $pageId
            AND $contentColumn[areaIndex] = $contentAreaIndex
            AND $contentColumn[position] >= $position";

        DBUtil::executeSQL($sql);

        Content_Util::clearCache();

        return true;
    }

    /* =[ Scanning and loading content type plugins ]================================= */

    public function getContentTypes($args)
    {
        $includeInactive = isset($args['includeInactive']) ? $args['includeInactive'] : false;
        $plugins = Content_Util::getPlugins('Content');
        $contentTypes = array();

        for ($i = 0, $cou = count($plugins); $i < $cou; ++$i) {
            $plugin = &$plugins[$i];
            if ($includeInactive || $plugin->isActive()) {
                $contentTypes[] = array(
                    'module' => $plugin->getModule(),
                    'name' => $plugin->getName(),
                    'title' => $plugin->getTitle(),
                    'description' => $plugin->getDescription(),
                    'adminInfo' => $plugin->getAdminInfo(),
                    'isActive' => $plugin->isActive());
            }
        }

        return $contentTypes;
    }

    // the passed $view argument is a Zikula_Form_View passed from the EditContent Form Handler
    public function getContentPlugin($args, Zikula_Form_View $view = null)
    {
        $classname = $args['module'] . "_ContentType_" . $args['type'];
        if (!class_exists($classname)) {
            return LogUtil::registerError($this->__f('Error! Unable to load plugin [%1$s] in module [%2$s] since the class is not defined. Upgrade of %2$s module required.', array($args['type'], $args['module'])));
        }

        if (!ModUtil::available($args['module'])) {
            return LogUtil::registerError($this->__f('Error! Unable to load plugin [%1$s] in module [%2$s] since the module is not available.', array($args['type'], $args['module'])));
        }

        $type = strtolower(FormUtil::getPassedValue('type', 'user'));
        if ($type == 'user') {
            $view = Zikula_View::getInstance($args['module']);
        } elseif ($type == 'admin' && $view == null) {
            $view = new Zikula_Form_View($this->getServiceManager(), $args['module']);
        } else {
            $view = Zikula_View::getInstance($args['module']);
        }

        if ($args['module'] != $view->getModuleName()) {
            $modinfo = ModUtil::getInfoFromName($args['module']);
            $modpath = $modinfo['type'] == ModUtil::TYPE_MODULE ? 'modules' : 'system';
            $view->addPluginDir("$modpath/{$modinfo['directory']}/templates/plugins");
        }

        $plugin = new $classname($view);
        if (empty($plugin)) {
            return LogUtil::registerError($this->__f('Error! Unable to load plugin [%1$s] in module [%2$s] for some unknown reason.', array($args['type'], $args['module'])));
        }
        $plugin->setcontentId($args['id']);
        $plugin->setPageId($args['pageId']);
        $plugin->setContentAreaIndex($args['areaIndex']);
        $plugin->setPosition($args['position']);
        $plugin->setStylePosition($args['stylePosition']);
        $plugin->setStyleWidth($args['styleWidth']);
        $plugin->setStyleClass($args['styleClass']);
        if (isset($args['data'])) {
            $plugin->loadData($args['data']);
        }

        return $plugin;
    }

    public function getContentType($args)
    {
        $plugin = $this->getContentPlugin($args);
        if ($plugin === false) {
            return false;
        }

        return array(
            'plugin' => &$plugin,
            'module' => $plugin->getModule(),
            'name' => $plugin->getName(),
            'title' => $plugin->getTitle(),
            'description' => $plugin->getDescription(),
            'adminInfo' => $plugin->getAdminInfo(),
            'isActive' => $plugin->isActive());
    }
}
