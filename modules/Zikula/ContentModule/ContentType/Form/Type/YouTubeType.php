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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zikula\Common\Content\AbstractContentFormType;
use Zikula\Common\Content\ContentTypeInterface;

/**
 * YouTube form type class.
 */
class YouTubeType extends AbstractContentFormType
{
    public function __construct(TranslatorInterface $translator)
    {
        $this->setTranslator($translator);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $context = $options['context'] ?? ContentTypeInterface::CONTEXT_EDIT;
        $builder
            ->add('url', UrlType::class, [
                'label' => $this->__('URL to the video clip') . ':',
                'help' => $this->__('Something like "https://www.youtube.com/watch?v=LIwJ0gCPLsg".')
            ])
            ->add('text', TextareaType::class, [
                'label' => $this->__('Video description') . ':',
                'required' => false
            ])
        ;
        if (ContentTypeInterface::CONTEXT_EDIT !== $context) {
            return;
        }
        $builder
            ->add('displayMode', ChoiceType::class, [
                'label' => $this->__('Display mode') . ':',
                'label_attr' => [
                    'class' => 'radio-inline'
                ],
                'choices' => [
                    $this->__('Show video inline') => 'inline',
                    $this->__('Show video in modal window') => 'modal'
                ],
                'expanded' => true
            ])
            ->add('noCookie', CheckboxType::class, [
                'label' => $this->__('Extended privacy mode') . ':',
                'required' => false
            ])
            ->add('showRelated', CheckboxType::class, [
                'label' => $this->__('Show related videos') . ':',
                'required' => false
            ])
            ->add('autoplay', CheckboxType::class, [
                'label' => $this->__('Autoplay the video when displayed') . ':',
                'required' => false
            ])
        ;
    }

    public function getBlockPrefix()
    {
        return 'zikulacontentmodule_contenttype_youtube';
    }
}
