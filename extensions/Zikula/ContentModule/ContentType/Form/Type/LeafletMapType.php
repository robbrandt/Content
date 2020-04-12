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

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zikula\ExtensionsModule\ModuleInterface\Content\ContentTypeInterface;
use Zikula\ExtensionsModule\ModuleInterface\Content\Form\Type\AbstractContentFormType;

/**
 * Leaflet map form type class.
 */
class LeafletMapType extends AbstractContentFormType
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
                ->add('height', IntegerType::class, [
                    'label' => 'Height of the displayed map:',
                    'attr' => [
                        'maxlength' => 4
                    ],
                    'input_group' => ['right' => 'pixels']
                ])
            ;
        }
        $builder->add('text', TextType::class, [
            'label' => 'Description to be shown below the map:',
            'attr' => [
                'maxlength' => 255
            ]
        ]);
        if (ContentTypeInterface::CONTEXT_EDIT === $context) {
            $builder
                ->add('tileLayerUrl', TextType::class, [
                    'label' => 'URL of tile layer to use:',
                    'help' => 'See <a href=\'%url%\' target="_blank">this page</a> for examples.',
                    'help_translation_parameters' => [
                        '%url%' => 'https://leaflet-extras.github.io/leaflet-providers/preview/'
                    ],
                    'help_html' => true,
                    'attr' => [
                        'maxlength' => 255
                    ]
                ])
                ->add('tileLayerAttribution', TextType::class, [
                    'label' => 'Attribution for tile layer to use:',
                    'attr' => [
                        'maxlength' => 255
                    ]
                ])
            ;
        }
    }

    public function getBlockPrefix()
    {
        return 'zikulacontentmodule_contenttype_leaflet';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'translation_domain' => 'contentTypes'
        ]);
    }
}
