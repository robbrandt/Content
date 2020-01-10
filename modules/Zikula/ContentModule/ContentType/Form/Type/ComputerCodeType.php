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

namespace Zikula\ContentModule\ContentType\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zikula\Bundle\CoreBundle\HttpKernel\ZikulaHttpKernelInterface;
use Zikula\Common\Content\AbstractContentFormType;

/**
 * Computer code form type class.
 */
class ComputerCodeType extends AbstractContentFormType
{
    /**
     * @var ZikulaHttpKernelInterface
     */
    protected $kernel;

    public function __construct(TranslatorInterface $translator, ZikulaHttpKernelInterface $kernel)
    {
        $this->setTranslator($translator);
        $this->kernel = $kernel;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $filterChoices = [
            $this->trans('Use native filter') => 'native'
        ];
        if ($this->kernel->isBundle('ZikulaBBCodeModule')) {
            $filterChoices[$this->trans('Use BBCode filter')] = 'bbcode';
        }
        if ($this->kernel->isBundle('PhaidonLuMicuLaModule')) {
            $filterChoices[$this->trans('Use LuMicuLa filter')] = 'lumicula';
        }

        $builder
            ->add('text', TextareaType::class, [
                'label' => $this->trans('Computer code lines') . ':'
            ])
            ->add('codeFilter', ChoiceType::class, [
                'label' => $this->trans('Code filter') . ':',
                'help' => $this->trans('If ZikulaBBCodeModule or PhaidonLuMicuLaModule are available, you can filter your code with them instead of the native filter. There is no need to hook these modules to Content for this functionality.'),
                'choices' => $filterChoices,
                'expanded' => true
            ])
        ;
    }

    public function getBlockPrefix()
    {
        return 'zikulacontentmodule_contenttype_computercode';
    }
}
