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
use Zikula\Common\Content\ContentTypeInterface;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\ContentModule\Entity\ContentItemEntity;
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

    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
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
                'panelClass' => $this->getWidgetPanelClass($item),
                'assets' => $contentType->getAssets(ContentTypeInterface::CONTEXT_VIEW),
                'jsEntryPoint' => $contentType->getJsEntrypoint(ContentTypeInterface::CONTEXT_VIEW)
            ];
        } catch (RuntimeException $exception) {
            return [
                'title' => '<i class="fa fa-exclamation-triangle"></i> ' . $this->__('Error'),
                'content' => $exception->getMessage(),
                'panelClass' => 'danger',
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
            throw new RuntimeException($this->__('Invalid content type received.') . ' ' . $contentTypeClass);
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
        $icon = '<i class="fa fa-' . $contentType->getIcon() . '"></i>';
        $title = $contentType->getTitle();
        $titleSuffix = '';

        if (!$item->isCurrentlyActive()) {
            $titleSuffix = ' (' . $this->__('inactive') . ')';
        } else {
            $scopes = $this->extractMultiList($item->getScope());
            foreach ($scopes as $scope) {
                if ('0' !== $scope) {
                    if ('-1' === $scope) {
                        $titleSuffix = ' (' . $this->__('only logged in members') . ')';
                    } elseif ('-2' === $scope) {
                        $titleSuffix = ' (' . $this->__('only not logged in people') . ')';
                    } else {
                        //$groupId = intval($scope);
                        //$group = $this->groupRepository->find($groupId);
                        //if (null !== $group) {
                        //    $titleSuffix = ' (' . $this->__f('only %group', ['%group' => $group->getName()]) . ')';
                        //} else {
                        $titleSuffix = ' (' . $this->__('specific group') . ')';
                        //}
                    }
                }
            }
        }

        $title .= $titleSuffix;

        return $icon . ' ' . $title;
    }

    /**
     * Returns the name of a bootstrap panel class for a given content item entity.
     */
    public function getWidgetPanelClass(ContentItemEntity $item): string
    {
        $result = 'default';

        if (!$item->isCurrentlyActive()) {
            $result = 'danger';
        } else {
            $scopes = $this->extractMultiList($item->getScope());
            foreach ($scopes as $scope) {
                if ('0' !== $scope) {
                    if ('-1' === $scope) {
                        $result = 'primary';
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