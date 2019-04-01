<?php

declare(strict_types=1);

/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 * @link https://ziku.la
 * @version Generated by ModuleStudio 1.4.0 (https://modulestudio.de).
 */

namespace Zikula\ContentModule\ContentType\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Zikula\Common\Content\AbstractContentFormType;
use Zikula\Common\Content\ContentTypeInterface;
use Zikula\Common\Translator\TranslatorInterface;

/**
 * Unfiltered raw form type class.
 */
class UnfilteredType extends AbstractContentFormType
{
    public function __construct(TranslatorInterface $translator)
    {
        $this->setTranslator($translator);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $context = $options['context'] ?? ContentTypeInterface::CONTEXT_EDIT;
        $builder->add('text', TextareaType::class, [
            'label' => $this->__('Unfiltered text') . ':',
            'required' => false
        ]);
        if (ContentTypeInterface::CONTEXT_EDIT === $context) {
            $builder
                ->add('useiframe', CheckboxType::class, [
                    'label' => $this->__('Use an iframe instead of the text field') . ':',
                    'help' => $this->__('If this setting is enabled the text field above will be ignored and iframe parameters will be used.'),
                    'required' => false
                ])
                ->add('iframeSrc', UrlType::class, [
                    'label' => $this->__('iframe src') . ':',
                    'help' => $this->__('the src parameter of the iframe'),
                    'required' => false,
                    'attr' => [
                        'maxlength' => 150
                    ]
                ])
            ;
        }
        $builder
            ->add('iframeName', TextType::class, [
                'label' => $this->__('iframe name parameter') . ':',
                'required' => false,
                'attr' => [
                    'maxlength' => 150
                ]
            ])
            ->add('iframeTitle', TextType::class, [
                'label' => $this->__('iframe title parameter') . ':',
                'required' => false,
                'attr' => [
                    'maxlength' => 150
                ]
            ])
        ;
        if (ContentTypeInterface::CONTEXT_EDIT === $context) {
            $builder
                ->add('iframeStyle', TextType::class, [
                    'label' => $this->__('iframe style') . ':',
                    'help' => $this->__('the style parameter of the iframe, e.g. "border:0"'),
                    'required' => false,
                    'attr' => [
                        'maxlength' => 150
                    ]
                ])
                ->add('iframeWidth', IntegerType::class, [
                    'label' => $this->__('iframe width') . ':',
                    'input_group' => ['right' => $this->__('pixels')],
                    'required' => false
                ])
                ->add('iframeHeight', IntegerType::class, [
                    'label' => $this->__('iframe height') . ':',
                    'input_group' => ['right' => $this->__('pixels')],
                    'required' => false
                ])
                ->add('iframeBorder', IntegerType::class, [
                    'label' => $this->__('iframe border') . ':',
                    'required' => false
                ])
                ->add('iframeScrolling', TextType::class, [
                    'label' => $this->__('iframe scrolling') . ':',
                    'help' => $this->__('the scrolling parameter of the iframe, e.g. "no"'),
                    'required' => false,
                    'attr' => [
                        'maxlength' => 20
                    ]
                ])
                ->add('iframeAllowTransparancy', CheckboxType::class, [
                    'label' => $this->__('Allow transparancy on the iframe') . ':',
                    'required' => false
                ])
            ;
        }
    }

    public function getBlockPrefix()
    {
        return 'zikulacontentmodule_contenttype_unfiltered';
    }
}
