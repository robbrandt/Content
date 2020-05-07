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

namespace Zikula\ContentModule;

use DateTime;
use Zikula\CategoriesModule\Entity\CategoryRegistryEntity;
use Zikula\ContentModule\Base\AbstractContentModuleInstaller;
use Zikula\ContentModule\Entity\PageEntity;
use Zikula\ContentModule\Entity\PageCategoryEntity;
use Zikula\ContentModule\Entity\ContentItemEntity;
use Zikula\ContentModule\Helper\ContentDisplayHelper;
use Zikula\UsersModule\Entity\RepositoryInterface\UserRepositoryInterface;

/**
 * Installer implementation class.
 */
class ContentModuleInstaller extends AbstractContentModuleInstaller
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var ContentDisplayHelper
     */
    private $contentDisplayHelper;

    public function install(): bool
    {
        $result = parent::install();
        if (!$result) {
            return $result;
        }

        $this->setVar('pageStyles', "product|Product page\nlegal|Legal page");
        $this->setVar('sectionStyles', "header|Header\nreferences|References\nfooter|Footer");
        $this->setVar(
            'contentStyles',
            "grey-box|Grey box\n"
            . "red-box|Red box\n"
            . "yellow-box|Yellow box\n"
            . "green-box|Green box\n"
            . "orange-announcement-box|Orange announcement box\n"
            . "green-important-box|Green important box"
        );

        return $result;
    }

    public function upgrade(string $oldVersion): bool
    {
        if (version_compare($oldVersion, '5.0.0', '<')) {
            ini_set('memory_limit', '2048M');
            ini_set('max_execution_time', 300); // 300 seconds = 5 minutes

            // delete all old data
            $this->variableApi->delAll('content');
            $this->variableApi->delAll('Content');

            // reinstall
            $this->install();

            // determine category registry identifier
            $categoryRegistries = $this->categoryRegistryRepository->findBy(['modname' => 'ZikulaContentModule']);
            $categoryRegistry = null;
            /** @var CategoryRegistryEntity $registry */
            foreach ($categoryRegistries as $registry) {
                if ('PageEntity' === $registry->getEntityname()) {
                    $categoryRegistry = $registry;
                    break;
                }
            }

            $connection = $this->entityManager->getConnection();
            $contentTypeNamespace = 'Zikula\\ContentModule\\ContentType\\';

            $pageMap = [];
            $pageLanguageMap = [];
            $categoryMap = [];
            $userMap = [];

            $mainPage = new PageEntity();
            $mainPage->setTitle($this->trans('Pages'));
            $mainPage->setLayout([]);
            $mainPage->setActive(true);
            $mainPage->setInMenu(true);
            $mainPage->setParent(null);
            $mainPage->setRoot(1);
            $this->entityManager->persist($mainPage);

            $item = new ContentItemEntity();
            $item->setOwningType($contentTypeNamespace . 'HeadingType');
            $item->setContentData([
                'text' => $this->trans('This is only a dummy page containing the real pages'),
                'headingType' => 'h3'
            ]);
            $mainPage->addContentItems($item);
            $this->entityManager->persist($item);
            $this->entityManager->flush();

            // migrate pages, primary category assignments, page translations
            $stmt = $connection->executeQuery("
                SELECT *
                FROM `content_page`
                ORDER BY `page_ppid`, `page_id`
            ");
            while ($row = $stmt->fetch()) {
                $oldPageId = $row['page_id'];

                $page = new PageEntity();
                $page->setWorkflowState('approved');
                $oldParentPageId = $row['page_ppid'];
                if ($oldParentPageId > 0 && isset($pageMap[$oldParentPageId])) {
                    $page->setParent($pageMap[$oldParentPageId]);
                } else {
                    $page->setParent($mainPage);
                }
                $page->setTitle($row['page_title']);
                $page->setShowTitle((bool)$row['page_showtitle']);
                $page->setMetaDescription($row['page_metadescription']);
                $page->setSkipHookSubscribers((bool)$row['page_nohooks']);
                $page->setViews((int)$row['page_views']);
                $page->setActive((bool)$row['page_active']);
                $activeFrom = $row['page_activefrom'];
                if (null !== $activeFrom && '' !== $activeFrom) {
                    $page->setActiveFrom(new DateTime($activeFrom));
                }
                $activeTo = $row['page_activeto'];
                if (null !== $activeTo && '' !== $activeTo) {
                    $page->setActiveTo(new DateTime($activeTo));
                }
                $page->setInMenu((bool)$row['page_inmenu']);
                if (isset($row['page_optString1'])) {
                    $page->setOptionalString1($row['page_optString1']);
                }
                if (isset($row['page_optString2'])) {
                    $page->setOptionalString2($row['page_optString2']);
                }
                if (isset($row['page_optText'])) {
                    $page->setOptionalText($row['page_optText']);
                }
                $uid = $row['page_cr_uid'];
                if (!isset($userMap[$uid])) {
                    $userMap[$uid] = $this->userRepository->find($uid);
                }
                $page->setCreatedBy($userMap[$uid]);
                $page->setCreatedDate(new DateTime($row['page_cr_date']));
                $uid = $row['page_lu_uid'];
                if (!isset($userMap[$uid])) {
                    $userMap[$uid] = $this->userRepository->find($uid);
                }
                $page->setUpdatedBy($userMap[$uid]);
                $page->setUpdatedDate(new DateTime($row['page_lu_date']));

                $page->setLocale($row['page_language']);
                $this->entityManager->persist($page);

                if (null !== $categoryRegistry) {
                    $categoryId = (int)$row['page_categoryid'];
                    if ($categoryId > 0) {
                        if (!isset($categoryMap[$categoryId])) {
                            $categoryMap[$categoryId] = $this->entityManager->find(
                                'ZikulaCategoriesModule:CategoryEntity',
                                $categoryId
                            );
                        }
                        // check if category still exists
                        if (null !== $categoryMap[$categoryId]) {
                            $categoryEntity = new PageCategoryEntity(
                                $categoryRegistry->getId(),
                                $categoryMap[$categoryId],
                                $page
                            );
                            $page->getCategories()->add($categoryEntity);
                        }
                    }
                }

                $this->entityManager->flush();

                $pageMap[$oldPageId] = $page;
                $pageLanguageMap[$oldPageId] = $page->getLocale();

                $transStmt = $connection->executeQuery("
                    SELECT `transp_lang`, `transp_title`, `transp_metadescription`
                    FROM `content_translatedpage`
                    WHERE `transp_pid` = " . (int)$oldPageId . '
                ');
                while ($transRow = $transStmt->fetch()) {
                    $page->setTitle($transRow['transp_title']);
                    $page->setMetaDescription($transRow['transp_metadescription']);

                    $page->setLocale($transRow['transp_lang']);
                    $this->entityManager->flush();
                }
            }

            // migrate content and content translations
            $stmt = $connection->executeQuery("
                SELECT *
                FROM `content_content`
                ORDER BY `con_pageid`, `con_areaindex`, `con_position`
            ");
            while ($row = $stmt->fetch()) {
                $oldContentItemId = $row['con_id'];
                $oldPageId = $row['con_pageid'];
                if (!isset($pageMap[$oldPageId])) {
                    continue;
                }

                $page = $pageMap[$oldPageId];

                $isSupported = true;
                if ('Content' !== $row['con_module']) {
                    $isSupported = false;
                } elseif (in_array($row['con_type'], ['Camtasia', 'FlashMovie', 'Flickr', 'JoinPosition'], true)) {
                    $isSupported = false;
                }

                $item = new ContentItemEntity();
                $item->setWorkflowState('approved');
                if (!$isSupported) {
                    $item->setOwningType($contentTypeNamespace . 'HtmlType');
                    $content = $this->trans(
                        '<p>There has been a <strong>%module%</strong> element with type <strong>%type%</strong> which could not be migrated during the Content module upgrade.</p>',
                        ['%module%' => $row['con_module'], '%type%' => $row['con_type']]
                    );
                    $item->setContentData(['text' => $content]);
                } else {
                    $contentTypeName = $row['con_type'] . 'Type';
                    if ('GoogleMapRouteType' === $contentTypeName) {
                        $contentTypeName = 'GoogleRouteType';
                    } elseif ('ModuleFuncType' === $contentTypeName) {
                        $contentTypeName = 'ControllerType';
                    } elseif ('OpenStreetMapType' === $contentTypeName) {
                        $contentTypeName = 'LeafletMapType';
                    } elseif ('RssType' === $contentTypeName) {
                        $contentTypeName = 'FeedType';
                    }
                    $item->setOwningType($contentTypeNamespace . $contentTypeName);

                    $contentData = @unserialize($row['con_data']);
                    if ($contentData) {
                        if ('AuthorType' === $contentTypeName && isset($contentData['uid'])) {
                            $contentData['author'] = $contentData['uid'];
                            unset($contentData['uid']);
                        } elseif ('BlockType' === $contentTypeName && isset($contentData['blockid'])) {
                            $contentData['blockId'] = $contentData['blockid'];
                            unset($contentData['blockid']);
                        } elseif ('ControllerType' === $contentTypeName && isset($contentData['arguments'])) {
                            $contentData['attributes'] = $contentData['arguments'];
                            unset($contentData['arguments']);
                        } elseif ('QuoteType' === $contentTypeName && isset($contentData['desc'])) {
                            $contentData['description'] = $contentData['desc'];
                            unset($contentData['desc']);
                        } elseif ('TableOfContentsType' === $contentTypeName && isset($contentData['pid'])) {
                            $oldPid = $contentData['pid'];
                            $contentData['page'] = isset($pageMap[$oldPid]) ? $pageMap[$oldPid]->getId() : 0;
                            unset($contentData['pid']);
                        }
                        $item->setContentData($contentData);
                    }
                }
                $item->setActive((bool)$row['con_active']);
                $scope = (int)$row['con_visiblefor'];
                if (1 > $scope) {
                    $item->setScope('-1');
                } elseif (2 === $scope) {
                    $item->setScope('-2');
                }
                if (!empty($row['con_styleclass'])) {
                    $item->setStylingClasses([$row['con_styleclass']]);
                }

                $contentType = $this->contentDisplayHelper->initContentType($item);
                $item->setSearchText($contentType->getSearchableText());

                $uid = $row['con_cr_uid'];
                if (!isset($userMap[$uid])) {
                    $userMap[$uid] = $this->userRepository->find($uid);
                }
                $item->setCreatedBy($userMap[$uid]);
                $item->setCreatedDate(new DateTime($row['con_cr_date']));
                $uid = $row['con_lu_uid'];
                if (!isset($userMap[$uid])) {
                    $userMap[$uid] = $this->userRepository->find($uid);
                }
                $item->setUpdatedBy($userMap[$uid]);
                $item->setUpdatedDate(new DateTime($row['con_lu_date']));

                $item->setLocale($pageLanguageMap[$oldPageId]);
                $page->addContentItems($item);

                $this->entityManager->persist($item);
                $this->entityManager->flush();
                if (!$isSupported) {
                    continue;
                }

                $transStmt = $connection->executeQuery("
                    SELECT `transc_lang`, `transc_data`
                    FROM `content_translatedcontent`
                    WHERE `transc_cid` = " . (int)$oldContentItemId . '
                ');
                while ($transRow = $transStmt->fetch()) {
                    $contentData = @unserialize($transRow['transc_data']);
                    if ($contentData) {
                        $contentData += $item->getContentData();
                        $item->setContentData($contentData);
                        $contentType = $this->contentDisplayHelper->initContentType($item);
                        $item->setSearchText($contentType->getSearchableText());
                    }

                    $item->setLocale($transRow['transc_lang']);
                    $this->entityManager->flush();
                }
            }

            // remove old tables
            $connection->executeQuery("DROP TABLE `content_history`");
            $connection->executeQuery("DROP TABLE `content_searchable`");
            $connection->executeQuery("DROP TABLE `content_translatedcontent`");
            $connection->executeQuery("DROP TABLE `content_content`");
            $connection->executeQuery("DROP TABLE `content_translatedpage`");
            $connection->executeQuery("DROP TABLE `content_pagecategory`");
            $connection->executeQuery("DROP TABLE `content_page`");

            $this->addFlash('success', $this->trans('Done! Migrated %amount% pages.', ['%amount%' => count($pageMap)]));

            return true;
        }

        switch ($oldVersion) {
            case '5.0.0':
            case '5.0.1':
            case '5.0.2':
            case '5.0.3':
                $connection = $this->entityManager->getConnection();

                // add scope field to pages
                $sql = '
                    ALTER TABLE `zikula_content_page`
                    ADD `scope` VARCHAR(100) NOT NULL
                    AFTER `activeTo`
                ';
                $stmt = $connection->prepare($sql);
                $stmt->execute();

                $sql = '
                    UPDATE `zikula_content_page`
                    SET scope = \'0\'
                ';
                $stmt = $connection->prepare($sql);
                $stmt->execute();

                // extend length of scope field of content items
                $sql = '
                    ALTER TABLE `zikula_content_contentitem`
                    MODIFY `scope` VARCHAR(100) NOT NULL
                ';
                $stmt = $connection->prepare($sql);
                $stmt->execute();
                // no break
            case '5.1.0':
                // nothing yet
            case '5.2.0':
                // future upgrades
        }

        // update successful
        return true;
    }

    /**
     * @required
     */
    public function setAdditionalDependencies(
        UserRepositoryInterface $userRepository,
        ContentDisplayHelper $contentDisplayHelper
    ) {
        $this->userRepository = $userRepository;
        $this->contentDisplayHelper = $contentDisplayHelper;
    }
}
