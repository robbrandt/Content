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

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zikula\ExtensionsModule\ModuleInterface\Content\ContentTypeInterface;
use Zikula\ExtensionsModule\ModuleInterface\Content\Form\Type\AbstractContentFormType;

/**
 * Unfiltered raw form type class.
 */
class UnfilteredType extends AbstractContentFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $context = $options['context'] ?? ContentTypeInterface::CONTEXT_EDIT;
        $builder->add('text', TextareaType::class, [
            'label' => 'Unfiltered text:',
            'required' => false
        ]);
        if (ContentTypeInterface::CONTEXT_EDIT === $context) {
            $builder
                ->add('useiframe', CheckboxType::class, [
                    'label' => 'Use an iframe instead of the text field:',
                    'label_attr' => ['class' => 'switch-custom'],
                    'help' => 'If this setting is enabled the text field above will be ignored and iframe parameters will be used.',
                    'required' => false
                ])
                ->add('iframeSrc', UrlType::class, [
                    'label' => 'iframe src:',
                    'help' => 'the src parameter of the iframe',
                    'required' => false,
                    'attr' => [
                        'maxlength' => 150
                    ]
                ])
            ;
        }
        $builder
            ->add('iframeName', TextType::class, [
                'label' => 'iframe name parameter:',
                'required' => false,
                'attr' => [
                    'maxlength' => 150
                ]
            ])
            ->add('iframeTitle', TextType::class, [
                'label' => 'iframe title parameter:',
                'required' => false,
                'attr' => [
                    'maxlength' => 150
                ]
            ])
        ;
        if (ContentTypeInterface::CONTEXT_EDIT === $context) {
            $builder
                ->add('iframeStyle', TextType::class, [
                    'label' => 'iframe style:',
                    'help' => 'the style parameter of the iframe, e.g. <code>border: 0</code>',
                    'help_html' => true,
                    'required' => false,
                    'attr' => [
                        'maxlength' => 150
                    ]
                ])
                ->add('iframeWidth', IntegerType::class, [
                    'label' => 'iframe width:',
                    'input_group' => ['right' => 'pixels'],
                    'required' => false
                ])
                ->add('iframeHeight', IntegerType::class, [
                    'label' => 'iframe height:',
                    'input_group' => ['right' => 'pixels'],
                    'required' => false
                ])
                ->add('iframeBorder', IntegerType::class, [
                    'label' => 'iframe border:',
                    'required' => false
                ])
                ->add('iframeScrolling', TextType::class, [
                    'label' => 'iframe scrolling:',
                    'help' => 'the scrolling parameter of the iframe, e.g. "no"',
                    'required' => false,
                    'attr' => [
                        'maxlength' => 20
                    ]
                ])
                ->add('iframeAllowTransparancy', CheckboxType::class, [
                    'label' => 'Allow transparancy on the iframe:',
                    'label_attr' => ['class' => 'switch-custom'],
                    'required' => false
                ])
            ;
        }
    }

    public function getBlockPrefix()
    {
        return 'zikulacontentmodule_contenttype_unfiltered';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'translation_domain' => 'contentTypes'
        ]);
    }
}
