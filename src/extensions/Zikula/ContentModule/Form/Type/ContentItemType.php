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

namespace Zikula\ContentModule\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Translation\Extractor\Annotation\Ignore;
use Translation\Extractor\Annotation\Translate;
use Zikula\ContentModule\Entity\ContentItemEntity;
use Zikula\ContentModule\Form\Type\Field\MultiListType;
use Zikula\ContentModule\Helper\ListEntriesHelper;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\ExtensionsModule\ModuleInterface\Content\ContentTypeInterface;

/**
 * Content item editing form type implementation class.
 */
class ContentItemType extends AbstractType
{
    /**
     * @var ListEntriesHelper
     */
    private $listHelper;

    /**
     * @var string
     */
    private $stylingClasses;

    public function __construct(
        ListEntriesHelper $listHelper,
        VariableApiInterface $variableApi
    ) {
        $this->listHelper = $listHelper;
        $this->stylingClasses = $variableApi->get('ZikulaContentModule', 'contentStyles', '');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (null !== $options['content_type']) {
            $editFormClass = $options['content_type']->getEditFormClass();
            if (null !== $editFormClass && '' !== $editFormClass && class_exists($editFormClass)) {
                $builder->add(
                    'contentData',
                    $editFormClass,
                    $options['content_type']->getEditFormOptions(ContentTypeInterface::CONTEXT_EDIT)
                );
            }
        }
        $builder->add('active', CheckboxType::class, [
            'label' => 'Active:',
            'label_attr' => ['class' => 'switch-custom'],
            'attr' => [
                'title' => 'active ?'
            ],
            'required' => false,
        ]);

        $builder->add('activeFrom', DateTimeType::class, [
            'label' => 'Active from:',
            'attr' => [
                'class' => 'validate-daterange-entity-page'
            ],
            'required' => false,
            'empty_data' => '',
            'with_seconds' => true,
            'date_widget' => 'single_text',
            'time_widget' => 'single_text'
        ]);

        $builder->add('activeTo', DateTimeType::class, [
            'label' => 'Active to:',
            'attr' => [
                'class' => 'validate-daterange-entity-page'
            ],
            'required' => false,
            'empty_data' => '',
            'with_seconds' => true,
            'date_widget' => 'single_text',
            'time_widget' => 'single_text'
        ]);

        $listEntries = $this->listHelper->getEntries('contentItem', 'scope');
        $choices = [];
        $choiceAttributes = [];
        foreach ($listEntries as $entry) {
            $choices[$entry['text']] = $entry['value'];
            $choiceAttributes[$entry['text']] = ['title' => $entry['title']];
        }
        $helpText = /** @Translate */'As soon as at least one selected entry applies for the current user the content becomes visible.';
        $builder->add('scope', MultiListType::class, [
            'label' => 'Scope:',
            'label_attr' => [
                'class' => 'tooltips checkbox-inline',
                'title' => $helpText
            ],
            /** @Ignore */
            'help' => $helpText,
            'empty_data' => '0',
            'attr' => [
                'class' => '',
                'title' => 'Choose the scope.'
            ],
            'required' => true,
            'choices' => $choices,
            'choice_attr' => $choiceAttributes,
            'multiple' => true,
            'expanded' => true
        ]);

        $choices = [];
        $userClasses = explode("\n", $this->stylingClasses);
        foreach ($userClasses as $class) {
            list($value, $text) = explode('|', $class);
            $value = trim($value);
            $text = trim($text);
            if (!empty($text) && !empty($value)) {
                $choices[$text] = $value;
            }
        }

        $builder->add('stylingClasses', ChoiceType::class, [
            'label' => 'Styling classes:',
            'empty_data' => [],
            'attr' => [
                'title' => 'Choose any additional styling classes.'
            ],
            'required' => false,
            'choices' => $choices,
            'multiple' => true
        ]);

        $helpText = /** @Translate */'You may enter any text which will be used during the site search to find this element.';
        $builder->add('additionalSearchText', TextType::class, [
            'label' => 'Additional search text:',
            'empty_data' => '',
            'attr' => [
                'maxlength' => 255,
                /** @Ignore */
                'title' => $helpText
            ],
            'required' => false,
            /** @Ignore */
            'help' => $helpText
        ]);
    }

    public function getBlockPrefix()
    {
        return 'zikulacontentmodule_contentitem';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ContentItemEntity::class,
            'translation_domain' => 'contentItem',
            'content_type' => null
        ]);
    }
}
