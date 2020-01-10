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
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zikula\Common\Content\AbstractContentFormType;
use Zikula\Common\Content\ContentTypeInterface;

/**
 * Google route form type class.
 */
class GoogleRouteType extends AbstractContentFormType
{
    public function __construct(TranslatorInterface $translator)
    {
        $this->setTranslator($translator);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $context = $options['context'] ?? ContentTypeInterface::CONTEXT_EDIT;
        if (ContentTypeInterface::CONTEXT_EDIT === $context) {
            $builder
                ->add('latitude', NumberType::class, [
                    'label' => $this->trans('Latitude') . ':',
                    'help' => $this->trans('A numeral that has a precision to 6 decimal places. For example, 40.714728.'),
                    'attr' => [
                        'maxlength' => 30
                    ]
                ])
                ->add('longitude', NumberType::class, [
                    'label' => $this->trans('Longitude') . ':',
                    'help' => $this->trans('A numeral that has a precision to 6 decimal places. For example, 40.714728.'),
                    'attr' => [
                        'maxlength' => 30
                    ]
                ])
                ->add('zoom', RangeType::class, [
                    'label' => $this->trans('Zoom level') . ':',
                    'help' => $this->trans('From 0 for the entire world to 21 for individual buildings.'),
                    'attr' => [
                        'min' => 0,
                        'max' => 21
                    ]
                ])
                ->add('mapType', ChoiceType::class, [
                    'label' => $this->trans('Map type') . ':',
                    'label_attr' => [
                        'class' => 'radio-inline'
                    ],
                    'choices' => [
                        $this->trans('Roadmap') => 'roadmap',
                        $this->trans('Satellite') => 'satellite',
                        $this->trans('Hybrid') => 'hybrid',
                        $this->trans('Terrain') => 'terrain'
                    ],
                    'expanded' => true
                ])
                ->add('height', IntegerType::class, [
                    'label' => $this->trans('Height of the displayed map') . ':',
                    'attr' => [
                        'maxlength' => 4
                    ],
                    'input_group' => ['right' => $this->trans('pixels')]
                ])
            ;
        }
        $builder
            ->add('addressText', TextType::class, [
                'label' => $this->trans('The target address') . ':',
                'attr' => [
                    'maxlength' => 255
                ]
            ])
            ->add('topText', TextType::class, [
                'label' => $this->trans('Header to be shown above the map') . ':',
                'required' => false
            ])
            ->add('bottomText', TextType::class, [
                'label' => $this->trans('Text to be shown below the map') . ':',
                'required' => false
            ])
        ;
    }

    public function getBlockPrefix()
    {
        return 'zikulacontentmodule_contenttype_googleroute';
    }
}
