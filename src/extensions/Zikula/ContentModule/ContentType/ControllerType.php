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

namespace Zikula\ContentModule\ContentType;

use Exception;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Zikula\Bundle\CoreBundle\HttpKernel\ZikulaHttpKernelInterface;
use Zikula\ContentModule\ContentType\Form\Type\ControllerType as FormType;
use Zikula\ExtensionsModule\ModuleInterface\Content\AbstractContentType;
use Zikula\ExtensionsModule\ModuleInterface\Content\ContentTypeInterface;

/**
 * Controller content type.
 */
class ControllerType extends AbstractContentType
{
    /**
     * @var ZikulaHttpKernelInterface
     */
    protected $kernel;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    public function getCategory(): string
    {
        return ContentTypeInterface::CATEGORY_EXPERT;
    }

    public function getIcon(): string
    {
        return 'cog';
    }

    public function getTitle(): string
    {
        return $this->translator->trans('Controller', [], 'contentTypes');
    }

    public function getDescription(): string
    {
        return $this->translator->trans('Display controller output from any installed module, theme or bundle.', [], 'contentTypes');
    }

    public function getDefaultData(): array
    {
        return [
            'controller' => [
                'controller' => '',
                'query' => '',
                'request' => '',
                'attributes' => '',
            ]
        ];
    }

    public function displayView(): string
    {
        $this->fetchContent();

        return parent::displayView();
    }

    public function displayEditing(): string
    {
        $output = $this->displayView();

        if ('' === $this->data['content'] && '' !== $this->data['noDisplayMessage']) {
            return '<p class="alert alert-info">' . $this->data['noDisplayMessage'] . '</p>';
        }

        $quickAction = '<a href="javascript:void(0);" title="'
            . $this->translator->trans('Preview controller content', [], 'contentTypes')
            . '" onclick="'
            . 'jQuery(this).parent().next(\'.d-none\').removeClass(\'d-none\'); '
            . 'jQuery(this).remove();'
            . '"><i class="fas fa-2x fa-eye"></i></a>'
        ;
        $pageInfo = $this->data['controller'];
        [$route, $controller] = explode('###', $pageInfo['controller']);
        $editOutput = '<h3>' . $route . '</h3>';
        $editOutput .= '<p>' . $controller . '</p>';
        if ($pageInfo['query']) {
            $editOutput .= '<p>' . $this->translator->trans('GET parameters', [], 'contentTypes')
                . ': <em>' . $pageInfo['query'] . '</em></p>'
            ;
        }
        if ($pageInfo['request']) {
            $editOutput .= '<p>' . $this->translator->trans('POST parameters', [], 'contentTypes')
                . ': <em>' . $pageInfo['request'] . '</em></p>'
            ;
        }
        if ($pageInfo['attributes']) {
            $editOutput .= '<p>' . $this->translator->trans('Request attributes', [], 'contentTypes')
                . ': <em>' . $pageInfo['attributes'] . '</em></p>'
            ;
        }
        $editOutput .= '<p>' . $quickAction . '</p>';
        $editOutput .= '<div class="d-none">' . $output . '</div>';

        return $editOutput;
    }

    /**
     * Retrieves output information.
     */
    protected function fetchContent(): void
    {
        $this->data['content'] = '';
        $this->data['noDisplayMessage'] = '';

        $pageInfo = $this->data['controller'];
        if (!is_array($pageInfo) || !isset($pageInfo['controller']) || empty($pageInfo['controller'])) {
            return;
        }

        [$route, $controller] = explode('###', $pageInfo['controller']);
        if (false === mb_strpos($controller, '\\') || false === mb_strpos($controller, '::')) {
            return;
        }

        [$vendor, $extensionName] = explode('\\', $controller);
        $extensionName = $vendor . $extensionName;
        [$fqcn, $method] = explode('::', $controller);
        if (!$this->kernel->isBundle($extensionName) || !class_exists($fqcn) || !is_callable([$fqcn, $method])) {
            $this->data['noDisplayMessage'] = $this->translator->trans(
                'Extension %extension% is not available.',
                ['%extension%' => $extensionName]
            );

            return;
        }

        try {
            $this->data['content'] = $this->callController();
        } catch (Exception $exception) {
            $this->data['content'] = '<p class="alert alert-danger">' . $exception->getMessage() . '</p>';
        }
    }

    /**
     * Calls the controller.
     */
    protected function callController(): string
    {
        static $recursionLevel = 0;
        if (4 < $recursionLevel) {
            return $this->translator->trans('Maximum number of pages-in-pages reached! You probably included this page in itself.', [], 'contentTypes');
        }

        $pageInfo = $this->data['controller'];
        [$route, $controller] = explode('###', $pageInfo['controller']);
        [$vendor, $extensionName] = explode('\\', $controller);
        $extensionName = $vendor . $extensionName;

        $queryParams = $requestParams = $attributes = [];
        if (null !== $pageInfo['query']) {
            parse_str($pageInfo['query'], $queryParams);
        }
        if (null !== $pageInfo['request']) {
            parse_str($pageInfo['request'], $requestParams);
        }
        if (null !== $pageInfo['attributes']) {
            parse_str($pageInfo['attributes'], $attributes);
        }
        $attributes['_controller'] = $controller;
        $attributes['_route'] = $route;

        $masterRequest = $this->requestStack->getMasterRequest();
        $masterRequest->attributes->set('_zkModule', $extensionName);
        $subRequest = $masterRequest->duplicate($queryParams, $requestParams, $attributes);

        ++$recursionLevel;

        return $this->kernel
            ->handle($subRequest, HttpKernelInterface::SUB_REQUEST)
            ->getContent();
    }

    public function getEditFormClass(): string
    {
        return FormType::class;
    }

    /**
     * @required
     */
    public function setAdditionalDepencies(
        ZikulaHttpKernelInterface $kernel,
        RequestStack $requestStack
    ): void {
        $this->kernel = $kernel;
        $this->requestStack = $requestStack;
    }
}
