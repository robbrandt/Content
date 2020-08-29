<?php

/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 *
 * @see https://ziku.la
 *
 * @version Generated by ModuleStudio 1.5.0 (https://modulestudio.de).
 */

declare(strict_types=1);

namespace Zikula\ContentModule\EntryProvider;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zikula\ContentModule\Entity\Factory\EntityFactory;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\ExtensionsModule\ModuleInterface\MultiHook\EntryProviderInterface;

/**
 * Page entry provider.
 */
class PageEntryProvider implements EntryProviderInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var EntityFactory
     */
    private $entityFactory;

    /**
     * Bundle name.
     *
     * @var string
     */
    private $bundleName;

    /**
     * The name of this provider.
     *
     * @var string
     */
    private $name;

    /**
     * Whether automatic page linking is enabled or not
     *
     * @var bool
     */
    private $enableAutomaticPageLinks;

    public function __construct(
        TranslatorInterface $translator,
        RouterInterface $router,
        EntityFactory $entityFactory,
        VariableApiInterface $variableApi
    ) {
        $this->translator = $translator;
        $this->router = $router;
        $this->entityFactory = $entityFactory;
        $this->enableAutomaticPageLinks = $variableApi->get('ZikulaContentModule', 'enableAutomaticPageLinks', true);

        $nsParts = explode('\\', static::class);
        $vendor = $nsParts[0];
        $nameAndType = $nsParts[1];

        $this->bundleName = $vendor . $nameAndType;
        $this->name = str_replace('Provider', '', array_pop($nsParts));
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIcon(): string
    {
        return 'book';
    }

    public function getTitle(): string
    {
        return $this->translator->trans('Pages', [], 'page');
    }

    public function getDescription(): string
    {
        return $this->translator->trans('Links page titles to corresponding pages.', [], 'page');
    }

    public function getAdminInfo(): string
    {
        return '';
    }

    public function isActive(): bool
    {
        return true;
    }

    public function getEntries(array $entryTypes = []): array
    {
        $result = [];
        if (true !== $this->enableAutomaticPageLinks) {
            return $result;
        }

        if (!in_array('link', $entryTypes, true)) {
            return $result;
        }

        $entities = $this->entityFactory->getRepository('page')
            ->selectWhere('', '', false, true);

        $routeName = 'zikulacontentmodule_page_display';
        foreach ($entities as $entity) {
            if (1 === $entity['id']) {
                continue;
            }
            $displayUrl = $this->router->generate(
                $routeName,
                ['slug' => $entity['slug']],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
            $result[] = [
                'longform' => $displayUrl,
                'shortform' => $entity['title'],
                'title' => $entity['title'],
                'type' => 'link',
                'language' => '', //$entity->getLocale()
            ];
        }

        return $result;
    }

    public function getBundleName(): string
    {
        return $this->bundleName;
    }
}
