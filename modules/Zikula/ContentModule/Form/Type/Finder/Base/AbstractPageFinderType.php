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

namespace Zikula\ContentModule\Form\Type\Finder\Base;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zikula\CategoriesModule\Form\Type\CategoriesType;
use Zikula\ContentModule\Helper\FeatureActivationHelper;

/**
 * Page finder form type base class.
 */
abstract class AbstractPageFinderType extends AbstractType
{
    /**
     * @var FeatureActivationHelper
     */
    protected $featureActivationHelper;

    public function __construct(
        FeatureActivationHelper $featureActivationHelper
    ) {
        $this->featureActivationHelper = $featureActivationHelper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('GET')
            ->add('objectType', HiddenType::class, [
                'data' => $options['object_type']
            ])
            ->add('editor', HiddenType::class, [
                'data' => $options['editor_name']
            ])
        ;

        if ($this->featureActivationHelper->isEnabled(FeatureActivationHelper::CATEGORIES, $options['object_type'])) {
            $this->addCategoriesField($builder, $options);
        }
        $this->addPasteAsField($builder, $options);
        $this->addSortingFields($builder, $options);
        $this->addAmountField($builder, $options);
        $this->addSearchField($builder, $options);

        $builder
            ->add('update', SubmitType::class, [
                'label' => 'Change selection',
                'icon' => 'fa-check',
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ])
            ->add('cancel', SubmitType::class, [
                'label' => 'Cancel',
                'validate' => false,
                'icon' => 'fa-times',
                'attr' => [
                    'class' => 'btn btn-default'
                ]
            ])
        ;
    }

    /**
     * Adds a categories field.
     */
    public function addCategoriesField(FormBuilderInterface $builder, array $options = []): void
    {
        $entityCategoryClass = 'Zikula\ContentModule\Entity\\' . ucfirst($options['object_type']) . 'CategoryEntity';
        $builder->add('categories', CategoriesType::class, [
            'label' => 'Category:',
            'empty_data' => null,
            'attr' => [
                'class' => 'category-selector',
                'title' => 'This is an optional filter.'
            ],
            'help' => 'This is an optional filter.',
            'required' => false,
            'multiple' => false,
            'module' => 'ZikulaContentModule',
            'entity' => ucfirst($options['object_type']) . 'Entity',
            'entityCategoryClass' => $entityCategoryClass,
            'showRegistryLabels' => true
        ]);
    }

    /**
     * Adds a "paste as" field.
     */
    public function addPasteAsField(FormBuilderInterface $builder, array $options = []): void
    {
        $builder->add('pasteAs', ChoiceType::class, [
            'label' => 'Paste as:',
            'empty_data' => 1,
            'choices' => [
                'Relative link to the page' => 1,
                'Absolute url to the page' => 2,
                'ID of page' => 3
            ],
            'multiple' => false,
            'expanded' => false
        ]);
    }

    /**
     * Adds sorting fields.
     */
    public function addSortingFields(FormBuilderInterface $builder, array $options = []): void
    {
        $builder
            ->add('sort', ChoiceType::class, [
                'label' => 'Sort by:',
                'empty_data' => '',
                'choices' => [
                    'Title' => 'title',
                    'Views' => 'views',
                    'Active' => 'active',
                    'Active from' => 'activeFrom',
                    'Active to' => 'activeTo',
                    'In menu' => 'inMenu',
                    'Optional string 1' => 'optionalString1',
                    'Optional string 2' => 'optionalString2',
                    'Current version' => 'currentVersion',
                    'Creation date' => 'createdDate',
                    'Creator' => 'createdBy',
                    'Update date' => 'updatedDate',
                    'Updater' => 'updatedBy'
                ],
                'multiple' => false,
                'expanded' => false
            ])
            ->add('sortdir', ChoiceType::class, [
                'label' => 'Sort direction:',
                'empty_data' => 'asc',
                'choices' => [
                    'Ascending' => 'asc',
                    'Descending' => 'desc'
                ],
                'multiple' => false,
                'expanded' => false
            ])
        ;
    }

    /**
     * Adds a page size field.
     */
    public function addAmountField(FormBuilderInterface $builder, array $options = []): void
    {
        $builder->add('num', ChoiceType::class, [
            'label' => 'Page size:',
            'empty_data' => 20,
            'attr' => [
                'class' => 'text-right'
            ],
            'choices' => [
                5 => 5,
                10 => 10,
                15 => 15,
                20 => 20,
                30 => 30,
                50 => 50,
                100 => 100
            ],
            'multiple' => false,
            'expanded' => false
        ]);
    }

    /**
     * Adds a search field.
     */
    public function addSearchField(FormBuilderInterface $builder, array $options = []): void
    {
        $builder->add('q', SearchType::class, [
            'label' => 'Search for:',
            'required' => false,
            'attr' => [
                'maxlength' => 255
            ]
        ]);
    }

    public function getBlockPrefix()
    {
        return 'zikulacontentmodule_pagefinder';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'object_type' => 'page',
                'editor_name' => 'ckeditor'
            ])
            ->setRequired(['object_type', 'editor_name'])
            ->setAllowedTypes('object_type', 'string')
            ->setAllowedTypes('editor_name', 'string')
            ->setAllowedValues('editor_name', ['ckeditor', 'quill', 'summernote', 'tinymce'])
        ;
    }
}
