<?php
/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 * @link https://ziku.la
 * @version Generated by ModuleStudio 1.3.2 (https://modulestudio.de).
 */

namespace Zikula\ContentModule\ContentType;

use \Twig_Environment;
use Symfony\Bundle\TwigBundle\Loader\FilesystemLoader;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Zikula\Bundle\CoreBundle\HttpKernel\ZikulaHttpKernelInterface;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\ContentModule\AbstractContentType;
use Zikula\ContentModule\ContentTypeInterface;
use Zikula\ContentModule\ContentType\Form\Type\ControllerType as FormType;
use Zikula\ContentModule\Helper\PermissionHelper;
use Zikula\ThemeModule\Engine\Asset;

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

    /**
     * ControllerType constructor.
     *
     * @param TranslatorInterface       $translator       Translator service instance
     * @param Twig_Environment          $twig             Twig service instance
     * @param FilesystemLoader          $twigLoader       Twig loader service instance
     * @param PermissionHelper          $permissionHelper PermissionHelper service instance
     * @param Asset                     $assetHelper      Asset service instance
     * @param ZikulaHttpKernelInterface $kernel           Kernel service instance
     * @param RequestStack              $requestStack     RequestStack service instance
     */
    public function __construct(
        TranslatorInterface $translator,
        Twig_Environment $twig,
        FilesystemLoader $twigLoader,
        PermissionHelper $permissionHelper,
        Asset $assetHelper,
        ZikulaHttpKernelInterface $kernel,
        RequestStack $requestStack
    ) {
        $this->kernel = $kernel;
        $this->requestStack = $requestStack;
        parent::__construct($translator, $twig, $twigLoader, $permissionHelper, $assetHelper);
    }

    /**
     * @inheritDoc
     */
    public function getCategory()
    {
        return ContentTypeInterface::CATEGORY_EXPERT;
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'cog';
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->__('Controller');
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->__('Display controller output from any installed module, theme or bundle.');
    }

    /**
     * @inheritDoc
     */
    public function getDefaultData()
    {
        return [
            'controller' => '',
            'arguments' => ''
        ];
    }

    /**
     * @inheritDoc
     */
    public function displayView()
    {
        $this->fetchContent();

        return parent::displayView();
    }

    /**
     * @inheritDoc
     */
    public function displayEditing()
    {
        $output = $this->displayView();

        if ('' == $this->data['content'] && '' != $this->data['noDisplayMessage']) {
            return '<p class="alert alert-info">' . $this->data['noDisplayMessage'] . '</p>';
        }

        $quickAction = '<a href="javascript:void(0);" title="' . $this->translator->__('Preview controller content') . '" onclick="jQuery(this).parent().next(\'.hidden\').removeClass(\'hidden\'); jQuery(this).remove();"><i class="fa fa-2x fa-eye"></i></a>';
        $editOutput = '<h3>' . $this->data['controller'] . '</h3>';
        if ($this->data['arguments']) {
            $editOutput .= '<p><em>' . $this->data['arguments'] . '</em></p>';
        }
        $editOutput .= '<p>' . $quickAction . '</p>';
        $editOutput .= '<div class="hidden">' . $output . '</div>';

        return $editOutput;
    }

    /**
     * Retrieves output information.
     */
    protected function fetchContent()
    {
        $this->data['content'] = '';
        $this->data['noDisplayMessage'] = '';

        $controller = $this->data['controller'];
        if (!$controller) {
            return;
        }

        list($bundleName) = explode(':', $controller);
        if (!$this->kernel->isBundle($bundleName)) {
            $this->data['noDisplayMessage'] = $this->translator->__f('Module %module is not available.', ['%module' => $bundleName]);
            return;
        }
        $moduleInstance = $this->kernel->getModule($bundleName);
        if (!isset($moduleInstance)) {
            $this->data['noDisplayMessage'] = $this->translator->__f('Module %module is not available.', ['%module' => $bundleName]);
            return;
        }

        try {
            $this->data['content'] = $this->callController();
        } catch (\Exception $exception) {
            $this->data['content'] = '<p class="alert alert-danger">' . $exception->getMessage() . '</p>';
        }
    }

    /**
     * Calls the controller.
     */
    protected function callController()
    {
        static $recursionLevel = 0;
        if ($recursionLevel > 4) {
            return $this->translator->__('Maximum number of pages-in-pages reached! You probably included this page in itself.');
        }

        $controller = $this->data['controller'];
        list($bundleName) = explode(':', $controller);

        parse_str($this->data['arguments'], $attributes);
        $attributes['_controller'] = $controller;
        $subRequest = $this->requestStack->getMasterRequest()->duplicate(null, null, $attributes);
        $subRequest->attributes->set('_zkModule', $bundleName);

        ++$recursionLevel;

        return $this->kernel
            ->handle($subRequest, HttpKernelInterface::SUB_REQUEST)
            ->getContent();

        --$recursionLevel;
    }

    /**
     * @inheritDoc
     */
    public function getEditFormClass()
    {
        return Formtype::class;
    }
}
