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

namespace Zikula\ContentModule\Block\Form\Type\Base;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zikula\CategoriesModule\Entity\RepositoryInterface\CategoryRepositoryInterface;
use Zikula\CategoriesModule\Form\Type\CategoriesType;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\ContentModule\Helper\FeatureActivationHelper;

/**
 * List block form type base class.
 */
abstract class AbstractItemListBlockType extends AbstractType
{
    use TranslatorTrait;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    public function __construct(
        TranslatorInterface $translator,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->setTranslator($translator);
        $this->categoryRepository = $categoryRepository;
    }

    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addObjectTypeField($builder, $options);
        if ($options['feature_activation_helper']->isEnabled(FeatureActivationHelper::CATEGORIES, $options['object_type'])) {
            $this->addCategoriesField($builder, $options);
        }
        $this->addSortingField($builder, $options);
        $this->addAmountField($builder, $options);
        $this->addTemplateFields($builder, $options);
        $this->addFilterField($builder, $options);
    }

    /**
     * Adds an object type field.
     */
    public function addObjectTypeField(FormBuilderInterface $builder, array $options = []): void
    {
        $builder->add('objectType', ChoiceType::class, [
            'label' => $this->__('Object type', 'zikulacontentmodule') . ':',
            'empty_data' => 'page',
            'attr' => [
                'title' => $this->__('If you change this please save the block once to reload the parameters below.', 'zikulacontentmodule')
            ],
            'help' => $this->__('If you change this please save the block once to reload the parameters below.', 'zikulacontentmodule'),
            'choices' => [
                $this->__('Pages', 'zikulacontentmodule') => 'page',
                $this->__('Content items', 'zikulacontentmodule') => 'contentItem'
            ],
            'multiple' => false,
            'expanded' => false
        ]);
    }

    /**
     * Adds a categories field.
     */
    public function addCategoriesField(FormBuilderInterface $builder, array $options = []): void
    {
        if (!$options['is_categorisable'] || null === $options['category_helper']) {
            return;
        }
    
        $objectType = $options['object_type'];
        $hasMultiSelection = $options['category_helper']->hasMultipleSelection($objectType);
        $builder->add('categories', CategoriesType::class, [
            'label' => ($hasMultiSelection ? $this->__('Categories', 'zikulacontentmodule') : $this->__('Category', 'zikulacontentmodule')) . ':',
            'empty_data' => $hasMultiSelection ? [] : null,
            'attr' => [
                'class' => 'category-selector',
                'title' => $this->__('This is an optional filter.', 'zikulacontentmodule')
            ],
            'help' => $this->__('This is an optional filter.', 'zikulacontentmodule'),
            'required' => false,
            'multiple' => $hasMultiSelection,
            'module' => 'ZikulaContentModule',
            'entity' => ucfirst($objectType) . 'Entity',
            'entityCategoryClass' => 'Zikula\ContentModule\Entity\\' . ucfirst($objectType) . 'CategoryEntity',
            'showRegistryLabels' => true
        ]);
    
        $categoryRepository = $this->categoryRepository;
        $builder->get('categories')->addModelTransformer(new CallbackTransformer(
            static function ($catIds) use ($categoryRepository, $objectType, $hasMultiSelection) {
                $categoryMappings = [];
                $entityCategoryClass = 'Zikula\ContentModule\Entity\\' . ucfirst($objectType) . 'CategoryEntity';
    
                $catIds = is_array($catIds) ? $catIds : explode(',', $catIds);
                foreach ($catIds as $catId) {
                    $category = $categoryRepository->find($catId);
                    if (null === $category) {
                        continue;
                    }
                    $mapping = new $entityCategoryClass(null, $category, null);
                    $categoryMappings[] = $mapping;
                }
    
                if (!$hasMultiSelection) {
                    $categoryMappings = 0 < count($categoryMappings) ? reset($categoryMappings) : null;
                }
    
                return $categoryMappings;
            },
            static function ($result) use ($hasMultiSelection) {
                $catIds = [];
    
                foreach ($result as $categoryMapping) {
                    $catIds[] = $categoryMapping->getCategory()->getId();
                }
    
                return $catIds;
            }
        ));
    }

    /**
     * Adds a sorting field.
     */
    public function addSortingField(FormBuilderInterface $builder, array $options = []): void
    {
        $builder->add('sorting', ChoiceType::class, [
            'label' => $this->__('Sorting', 'zikulacontentmodule') . ':',
            'empty_data' => 'default',
            'choices' => [
                $this->__('Random', 'zikulacontentmodule') => 'random',
                $this->__('Newest', 'zikulacontentmodule') => 'newest',
                $this->__('Updated', 'zikulacontentmodule') => 'updated',
                $this->__('Default', 'zikulacontentmodule') => 'default'
            ],
            'multiple' => false,
            'expanded' => false
        ]);
    }

    /**
     * Adds a page size field.
     */
    public function addAmountField(FormBuilderInterface $builder, array $options = []): void
    {
        $builder->add('amount', IntegerType::class, [
            'label' => $this->__('Amount', 'zikulacontentmodule') . ':',
            'attr' => [
                'maxlength' => 2,
                'title' => $this->__('The maximum amount of items to be shown.', 'zikulacontentmodule') . ' ' . $this->__('Only digits are allowed.', 'zikulacontentmodule')
            ],
            'help' => $this->__('The maximum amount of items to be shown.', 'zikulacontentmodule') . ' ' . $this->__('Only digits are allowed.', 'zikulacontentmodule'),
            'empty_data' => 5,
            'scale' => 0
        ]);
    }

    /**
     * Adds template fields.
     */
    public function addTemplateFields(FormBuilderInterface $builder, array $options = []): void
    {
        $builder
            ->add('template', ChoiceType::class, [
                'label' => $this->__('Template', 'zikulacontentmodule') . ':',
                'empty_data' => 'itemlist_display.html.twig',
                'choices' => [
                    $this->__('Only item titles', 'zikulacontentmodule') => 'itemlist_display.html.twig',
                    $this->__('With description', 'zikulacontentmodule') => 'itemlist_display_description.html.twig',
                    $this->__('Custom template', 'zikulacontentmodule') => 'custom'
                ],
                'multiple' => false,
                'expanded' => false
            ])
            ->add('customTemplate', TextType::class, [
                'label' => $this->__('Custom template', 'zikulacontentmodule') . ':',
                'required' => false,
                'attr' => [
                    'maxlength' => 80,
                    'title' => $this->__('Example', 'zikulacontentmodule') . ': itemlist_[objectType]_display.html.twig'
                ],
                'help' => $this->__('Example', 'zikulacontentmodule') . ': <em>itemlist_[objectType]_display.html.twig</em>'
            ])
        ;
    }

    /**
     * Adds a filter field.
     */
    public function addFilterField(FormBuilderInterface $builder, array $options = []): void
    {
        $builder->add('filter', TextType::class, [
            'label' => $this->__('Filter (expert option)', 'zikulacontentmodule') . ':',
            'required' => false,
            'attr' => [
                'maxlength' => 255,
                'title' => $this->__('Example', 'zikulacontentmodule') . ': tbl.age >= 18'
            ],
            'help' => $this->__('Example', 'zikulacontentmodule') . ': tbl.age >= 18'
        ]);
    }

    public function getBlockPrefix()
    {
        return 'zikulacontentmodule_listblock';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'object_type' => 'page',
                'is_categorisable' => false,
                'category_helper' => null,
                'feature_activation_helper' => null
            ])
            ->setRequired(['object_type'])
            ->setDefined(['is_categorisable', 'category_helper', 'feature_activation_helper'])
            ->setAllowedTypes('object_type', 'string')
            ->setAllowedTypes('is_categorisable', 'bool')
            ->setAllowedTypes('category_helper', 'object')
            ->setAllowedTypes('feature_activation_helper', 'object')
        ;
    }
}
