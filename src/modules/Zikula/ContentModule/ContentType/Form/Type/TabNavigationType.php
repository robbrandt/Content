<?php
/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 * @link https://ziku.la
 * @version Generated by ModuleStudio 1.3.2 (https://modulestudio.de).
 */

namespace Zikula\ContentModule\ContentType\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;

/**
 * Tab navigation form type class.
 */
class TabNavigationType extends AbstractType
{
    use TranslatorTrait;

    /**
     * TabNavigationType constructor.
     *
     * @param TranslatorInterface $translator Translator service instance
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->setTranslator($translator);
    }

    /**
     * Sets the translator.
     *
     * @param TranslatorInterface $translator Translator service instance
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contentItemIds', TextType::class, [
                'label' => $this->__('Content item IDs') . ':',
                'help' => $this->__('A list of Content item IDs semicolon separated, e.g. "3;12". Make sure that the Content item IDs you select already exist. You can disable the individual Content items if you only want to display them in this tab navigation.')
            ])
            ->add('tabTitles', TextType::class, [
                'label' => $this->__('Tab titles') . ':',
                'help' => $this->__('Titles for the tabs, semicolon separated, e.g. "Recent News;Calender".')
            ])
            ->add('tabLinks', TextType::class, [
                'label' => $this->__('Link names') . ':',
                'help' => $this->__('Internal named links for the tabs, semicolon separated and no spaces, e.g. "news;calendar".')
            ])
            ->add('tabType', ChoiceType::class, [
                'label' => $this->__('Navigation type') . ':',
                'choices' => [
                    $this->__('Tabs') => '1',
                    $this->__('Pills') => '2',
                    $this->__('Stacked pills') . ' (col-sm3/col-sm-9)' => '3'
                ]
            ])
            ->add('tabStyle', TextType::class, [
                'label' => $this->__('Custom style class') . ':',
                'help' => $this->__('A CSS class name that will be used on the tab navigation.'),
                'required' => false,
                'attr' => [
                    'maxlength' => 50
                ]
            ])
        ;
    }

    /**
     * @inheritDoc
     */
    public function getBlockPrefix()
    {
        return 'zikulacontentmodule_contenttype_tabnavigation';
    }
}
