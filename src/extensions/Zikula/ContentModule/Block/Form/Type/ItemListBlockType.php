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

namespace Zikula\ContentModule\Block\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Zikula\CategoriesModule\Entity\RepositoryInterface\CategoryRepositoryInterface;
use Zikula\ContentModule\Block\Form\Type\Base\AbstractItemListBlockType;
use Zikula\ContentModule\Entity\Factory\EntityFactory;
use Zikula\ContentModule\Entity\PageEntity;
use Zikula\ContentModule\Form\DataTransformer\PageTransformer;
use Zikula\ContentModule\Form\Type\Field\EntityTreeType;
use Zikula\ContentModule\Helper\FeatureActivationHelper;

/**
 * List block form type implementation class.
 */
class ItemListBlockType extends AbstractItemListBlockType
{
    /**
     * @var EntityFactory
     */
    protected $entityFactory;

    public function __construct(
        EntityFactory $entityFactory,
        CategoryRepositoryInterface $categoryRepository
    ) {
        parent::__construct($categoryRepository);
        $this->entityFactory = $entityFactory;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('root', EntityTreeType::class, [
            'class' => PageEntity::class,
            'multiple' => false,
            'expanded' => false,
            'use_joins' => false,
            'placeholder' => 'All pages',
            'required' => false,
            'label' => 'Include the following subpages:'
        ]);
        $transformer = new PageTransformer($this->entityFactory);
        $builder->get('root')->addModelTransformer($transformer);

        if (
            $options['feature_activation_helper']->isEnabled(
                FeatureActivationHelper::CATEGORIES,
                $options['object_type']
            )
        ) {
            $this->addCategoriesField($builder, $options);
        }
        $this->addSortingField($builder, $options);
        $this->addAmountField($builder, $options);
        $builder->add('inMenu', CheckboxType::class, [
            'label' => 'Use only pages activated for the menu:',
            'label_attr' => ['class' => 'switch-custom'],
            'required' => false
        ]);
        $this->addFilterField($builder, $options);
    }

    public function addSortingField(FormBuilderInterface $builder, array $options = []): void
    {
        $builder->add('sorting', ChoiceType::class, [
            'label' => 'Sorting:',
            'empty_data' => 'default',
            'choices' => [
                'Random' => 'random',
                'Newest' => 'newest',
                'Updated' => 'updated',
                'Views' => 'views',
                'Default' => 'default'
            ],
            'multiple' => false,
            'expanded' => false
        ]);
    }
}
