<?php

/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 * @see https://ziku.la
 * @version Generated by ModuleStudio 1.5.0 (https://modulestudio.de).
 */

declare(strict_types=1);

namespace Zikula\ContentModule\ContentType\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zikula\ExtensionsModule\ModuleInterface\Content\ContentTypeInterface;
use Zikula\ExtensionsModule\ModuleInterface\Content\Form\Type\AbstractContentFormType;

/**
 * Google map form type class.
 */
class GoogleMapType extends AbstractContentFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $context = $options['context'] ?? ContentTypeInterface::CONTEXT_EDIT;
        if (ContentTypeInterface::CONTEXT_EDIT === $context) {
            $builder
                ->add('latitude', NumberType::class, [
                    'label' => 'Latitude:',
                    'help' => 'A numeral that has a precision to 6 decimal places. For example, 40.714728.',
                    'attr' => [
                        'maxlength' => 30
                    ]
                ])
                ->add('longitude', NumberType::class, [
                    'label' => 'Longitude:',
                    'help' => 'A numeral that has a precision to 6 decimal places. For example, 40.714728.',
                    'attr' => [
                        'maxlength' => 30
                    ]
                ])
                ->add('zoom', RangeType::class, [
                    'label' => 'Zoom level:',
                    'help' => 'From 0 for the entire world to 21 for individual buildings.',
                    'attr' => [
                        'min' => 0,
                        'max' => 21
                    ]
                ])
                ->add('mapType', ChoiceType::class, [
                    'label' => 'Map type:',
                    'label_attr' => [
                        'class' => 'radio-inline'
                    ],
                    'choices' => [
                        'Roadmap' => 'roadmap',
                        'Satellite' => 'satellite',
                        'Hybrid' => 'hybrid',
                        'Terrain' => 'terrain'
                    ],
                    'expanded' => true
                ])
                ->add('height', IntegerType::class, [
                    'label' => 'Height of the displayed map:',
                    'attr' => [
                        'maxlength' => 4
                    ],
                    'input_group' => ['right' => 'pixels']
                ])
            ;
        }
        $builder
            ->add('text', TextType::class, [
                'label' => 'Description to be shown below the map:',
                'attr' => [
                    'maxlength' => 255
                ]
            ])
            ->add('infoText', TextareaType::class, [
                'label' => 'Text to be shown in the popup window of the marker:',
                'help' => 'Can contain HTML markup. Leave this field empty for disabling the popup window.',
                'required' => false
            ])
        ;
        if (ContentTypeInterface::CONTEXT_EDIT === $context) {
            $builder
                ->add('trafficOverlay', CheckboxType::class, [
                    'label' => 'Display a traffic overlay:',
                    'label_attr' => ['class' => 'switch-custom'],
                    'required' => false
                ])
                ->add('bicycleOverlay', CheckboxType::class, [
                    'label' => 'Display a bicycle overlay:',
                    'label_attr' => ['class' => 'switch-custom'],
                    'required' => false
                ])
                ->add('streetViewControl', CheckboxType::class, [
                    'label' => 'Display the streetview control:',
                    'label_attr' => ['class' => 'switch-custom'],
                    'required' => false
                ])
                ->add('directionsLink', CheckboxType::class, [
                    'label' => 'Display a link to directions to this location in Google Maps:',
                    'label_attr' => ['class' => 'switch-custom'],
                    'required' => false
                ])
                ->add('directionsInline', CheckboxType::class, [
                    'label' => 'Display directions inline within the map:',
                    'label_attr' => ['class' => 'switch-custom'],
                    'required' => false
                ])
            ;
        }
    }

    public function getBlockPrefix()
    {
        return 'zikulacontentmodule_contenttype_googlemap';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'translation_domain' => 'contentTypes'
        ]);
    }
}
