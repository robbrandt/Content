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

namespace Zikula\ContentModule\Block\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\ContentModule\Entity\Factory\EntityFactory;
use Zikula\ContentModule\Entity\PageEntity;
use Zikula\ContentModule\Form\DataTransformer\PageTransformer;
use Zikula\ContentModule\Form\Type\Field\EntityTreeType;

/**
 * Menu block form type implementation class.
 */
class MenuBlockType extends AbstractType
{
    use TranslatorTrait;

    /**
     * @var EntityFactory
     */
    protected $entityFactory;

    public function __construct(
        TranslatorInterface $translator,
        EntityFactory $entityFactory
    ) {
        $this->setTranslator($translator);
        $this->entityFactory = $entityFactory;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('navType', ChoiceType::class, [
            'label' => $this->__('Navigation type', 'zikulacontentmodule') . ':',
            'choices' => [
                $this->__('None', 'zikulacontentmodule') => '0',
                $this->__('Tabs', 'zikulacontentmodule') => '1',
                $this->__('Pills', 'zikulacontentmodule') => '2',
                $this->__('Navbar', 'zikulacontentmodule') => '3'
            ]
        ]);
        $builder->add('subPagesHandling', ChoiceType::class, [
            'label' => $this->__('Sub pages handling', 'zikulacontentmodule') . ':',
            'choices' => [
                $this->__('Hide them', 'zikulacontentmodule') => 'hide',
                $this->__('Use dropdowns', 'zikulacontentmodule') => 'dropdown'
            ]
        ]);
        $builder->add('root', EntityTreeType::class, [
            'class' => PageEntity::class,
            'multiple' => false,
            'expanded' => false,
            'use_joins' => false,
            'placeholder' => $this->__('All pages', 'zikulacontentmodule'),
            'required' => false,
            'label' => $this->__('Include the following subpages', 'zikulacontentmodule') . ':'
        ]);
        $transformer = new PageTransformer($this->entityFactory);
        $builder->get('root')->addModelTransformer($transformer);
        $helpText = $this->__('The maximum amount of items to be shown.', 'zikulacontentmodule')
            . ' '
            . $this->__('Only digits are allowed.', 'zikulacontentmodule')
        ;
        $builder->add('amount', IntegerType::class, [
            'label' => $this->__('Amount', 'zikulacontentmodule') . ':',
            'attr' => [
                'maxlength' => 2,
                'title' => $helpText
            ],
            'help' => $helpText,
            'empty_data' => 5
        ]);
        $builder->add('inMenu', CheckboxType::class, [
            'label' => $this->__('Use only pages activated for the menu', 'zikulacontentmodule') . ':',
            'label_attr' => ['class' => 'switch-custom'],
            'required' => false
        ]);
        $builder->add('filter', TextType::class, [
            'label' => $this->__('Filter (expert option)', 'zikulacontentmodule') . ':',
            'required' => false,
            'attr' => [
                'maxlength' => 255,
                'title' => $this->__('Example', 'zikulacontentmodule') . ': tbl.age >= 18'
            ],
            'help' => $this->__('Example', 'zikulacontentmodule') . ': <code>tbl.age >= 18</code>',
            'help_html' => true
        ]);
    }

    public function getBlockPrefix()
    {
        return 'zikulacontentmodule_menublock';
    }
}
