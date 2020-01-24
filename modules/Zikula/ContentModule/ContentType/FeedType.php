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

namespace Zikula\ContentModule\ContentType;

use SimplePie;
use Zikula\ContentModule\ContentType\Form\Type\FeedType as FormType;
use Zikula\ContentModule\Helper\CacheHelper;
use Zikula\ExtensionsModule\ModuleInterface\Content\AbstractContentType;
use Zikula\ExtensionsModule\ModuleInterface\Content\ContentTypeInterface;

/**
 * Feed content type.
 */
class FeedType extends AbstractContentType
{
    /**
     * @var CacheHelper
     */
    protected $cacheHelper;

    public function getCategory(): string
    {
        return ContentTypeInterface::CATEGORY_EXTERNAL;
    }

    public function getIcon(): string
    {
        return 'rss-square';
    }

    public function getTitle(): string
    {
        return $this->translator->trans('Feed', [], 'contentTypes');
    }

    public function getDescription(): string
    {
        return $this->translator->trans('Display list of items in an Atom or RSS feed.', [], 'contentTypes');
    }

    public function getDefaultData(): array
    {
        return [
            'url' => '',
            'includeContent' => false,
            'refreshTime' => 2,
            'maxNoOfItems' => 10
        ];
    }

    public function getSearchableText(): string
    {
        return html_entity_decode(strip_tags($this->data['url']));
    }

    public function displayView(): string
    {
        $cacheDirectory = $this->cacheHelper->getCacheDirectory();
        $feed = new SimplePie();
        if (file_exists($cacheDirectory)) {
            $feed->set_cache_location($cacheDirectory);
            $feed->set_cache_duration($this->data['refreshTime'] * 60 * 60);
        } else {
            $feed->enable_cache(false);
        }
        $feed->set_feed_url($this->data['url']);
        if (true !== $feed->init()) {
            return '';
        }

        $feedItems = $feed->get_items();
        $feedEncoding = $feed->get_encoding();

        $items = [];
        foreach ($feedItems as $item) {
            if (count($items) >= $this->data['maxNoOfItems']) {
                break;
            }

            $items[] = [
                'title' => $this->decode($item->get_title(), $feedEncoding),
                'description' => strip_tags(html_entity_decode($this->decode($item->get_description(), $feedEncoding))),
                'permalink' => $item->get_permalink()
            ];
        }

        $this->data['feed'] = [
            'title' => $this->decode($feed->get_title(), $feedEncoding),
            'description' => strip_tags(html_entity_decode($this->decode($feed->get_description(), $feedEncoding))),
            'permalink' => $feed->get_permalink(),
            'items' => $items
        ];

        return parent::displayView();
    }

    public function getEditFormClass(): string
    {
        return FormType::class;
    }

    protected function decode(string $string, string $feedEncoding): string
    {
        return mb_convert_encoding($string, mb_detect_encoding($string), $feedEncoding);
    }

    /**
     * @required
     */
    public function setAdditionalDepencies(
        CacheHelper $cacheHelper
    ): void {
        $this->cacheHelper = $cacheHelper;
    }
}
