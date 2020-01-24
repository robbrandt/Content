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

namespace Zikula\ContentModule\Helper;

use Psr\Container\ContainerInterface;
use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zikula\Bundle\CoreBundle\Translation\TranslatorTrait;
use Zikula\ContentModule\Entity\ContentItemEntity;
use Zikula\ExtensionsModule\ModuleInterface\Content\ContentTypeInterface;
use Zikula\GroupsModule\Entity\RepositoryInterface\GroupRepositoryInterface;
use Zikula\ThemeModule\Api\ApiInterface\PageAssetApiInterface;

/**
 * Helper class for displaying content items.
 */
class ContentDisplayHelper implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use TranslatorTrait;

    /**
     * @var PageAssetApiInterface
     */
    protected $pageAssetApi;

    /**
     * @var GroupRepositoryInterface
     */
    protected $groupRepository;

    public function __construct(
        ContainerInterface $container,
        TranslatorInterface $translator,
        PageAssetApiInterface $pageAssetApi,
        GroupRepositoryInterface $groupRepository
    ) {
        $this->setContainer($container);
        $this->setTranslator($translator);
        $this->pageAssetApi = $pageAssetApi;
        $this->groupRepository = $groupRepository;
    }

    /**
     * Returns all required details for display view of a content item.
     */
    public function prepareForDisplay(
        ContentItemEntity $item,
        string $context = ContentTypeInterface::CONTEXT_VIEW
    ): string {
        $contentType = $this->initContentType($item);

        $assets = $contentType->getAssets($context);
        if (isset($assets['css']) && is_array($assets['css'])) {
            foreach ($assets['css'] as $path) {
                $this->pageAssetApi->add('stylesheet', $path);
            }
        }
        if (isset($assets['js']) && is_array($assets['js'])) {
            foreach ($assets['js'] as $path) {
                $this->pageAssetApi->add('javascript', $path);
            }
        }
        $jsEntryPoint = $contentType->getJsEntrypoint($context);
        if (null !== $jsEntryPoint) {
            $initScript = "
                <script>
                    (function($) {
                        $(document).ready(function() {
                            if ('function' === typeof " . $jsEntryPoint . ') {
                                ' . $jsEntryPoint . '();
                            }
                        });
                    })(jQuery)
                </script>
            ';
            $this->pageAssetApi->add('footer', $initScript);
        }

        if (ContentTypeInterface::CONTEXT_VIEW === $context) {
            return $contentType->display(false);
        }

        return '';
    }

    /**
     * Returns all required details for editing view of a content item.
     */
    public function getDetailsForDisplayEditing(ContentItemEntity $item): array
    {
        try {
            $contentType = $this->initContentType($item);

            return [
                'title' => $this->getWidgetTitle($item, $contentType),
                'content' => $contentType->display(true),
                'cardClass' => $this->getWidgetCardClass($item),
                'assets' => $contentType->getAssets(ContentTypeInterface::CONTEXT_VIEW),
                'jsEntryPoint' => $contentType->getJsEntrypoint(ContentTypeInterface::CONTEXT_VIEW)
            ];
        } catch (RuntimeException $exception) {
            return [
                'title' => '<i class="fas fa-exclamation-triangle"></i> ' . $this->trans('Error'),
                'content' => $exception->getMessage(),
                'cardClass' => 'danger',
                'assets' => [],
                'jsEntryPoint' => null
            ];
        }
    }

    /**
     * Initialises a content type instance for a given content item.
     */
    public function initContentType(ContentItemEntity $item): ContentTypeInterface
    {
        $contentTypeClass = $item->getOwningType();
        if (!class_exists($contentTypeClass) || !$this->container->has($contentTypeClass)) {
            throw new RuntimeException($this->trans('Invalid content type received.', [], 'contentItem') . ' ' . $contentTypeClass);
        }

        $contentType = $this->container->get($contentTypeClass);
        $contentType->setEntity($item);

        return $contentType;
    }

    /**
     * Returns the title for the widget of a given content item entity.
     */
    public function getWidgetTitle(ContentItemEntity $item, ContentTypeInterface $contentType): string
    {
        $icon = '<i class="fas fa-' . $contentType->getIcon() . '"></i>';
        $title = $contentType->getTitle();
        $titleSuffix = '';

        if (!$item->isCurrentlyActive()) {
            $titleSuffix = ' (' . $this->trans('inactive', [], 'contentItem') . ')';
        } else {
            $scopes = $this->extractMultiList($item->getScope());
            foreach ($scopes as $scope) {
                if ('0' !== $scope) {
                    if ('-1' === $scope) {
                        $titleSuffix = ' (' . $this->trans('only logged in members', [], 'contentItem') . ')';
                    } elseif ('-2' === $scope) {
                        $titleSuffix = ' (' . $this->trans('only not logged in people', [], 'contentItem') . ')';
                    } else {
                        //$groupId = intval($scope);
                        //$group = $this->groupRepository->find($groupId);
                        //if (null !== $group) {
                        //    $titleSuffix = ' (' . $this->trans('only %group%', ['%group%' => $group->getName()], 'contentItem') . ')';
                        //} else {
                        $titleSuffix = ' (' . $this->trans('specific group', [], 'contentItem') . ')';
                        //}
                    }
                }
            }
        }

        $title .= $titleSuffix;

        return $icon . ' ' . $title;
    }

    /**
     * Returns the name of a bootstrap card class for a given content item entity.
     */
    public function getWidgetCardClass(ContentItemEntity $item): string
    {
        $result = 'default';

        if (!$item->isCurrentlyActive()) {
            $result = 'danger';
        } else {
            $scopes = $this->extractMultiList($item->getScope());
            foreach ($scopes as $scope) {
                if ('0' !== $scope) {
                    if ('-1' === $scope) {
                        $result = 'secondary';
                    } elseif ('-2' === $scope) {
                        $result = 'success';
                    } elseif ('1' === $scope || '2' === $scope) {
                        $result = 'warning';
                    } else {
                        $result = 'info';
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Extract concatenated multi selection.
     */
    public function extractMultiList(string $value): array
    {
        $listValues = explode('###', $value);
        $amountOfValues = count($listValues);
        if (1 < $amountOfValues && '' === $listValues[$amountOfValues - 1]) {
            unset($listValues[$amountOfValues - 1]);
        }
        if ('' === $listValues[0]) {
            // use array_shift instead of unset for proper key reindexing
            // keys must start with 0, otherwise the dropdownlist form plugin gets confused
            array_shift($listValues);
        }
    
        return $listValues;
    }
}
