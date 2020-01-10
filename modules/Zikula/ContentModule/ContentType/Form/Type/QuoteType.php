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

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zikula\Common\Content\AbstractContentFormType;

/**
 * Quote form type class.
 */
class QuoteType extends AbstractContentFormType
{
    public function __construct(TranslatorInterface $translator)
    {
        $this->setTranslator($translator);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', TextareaType::class, [
                'label' => $this->__('Quote text') . ':'
            ])
            ->add('source', UrlType::class, [
                'label' => $this->__('Source') . ':',
                'required' => false
            ])
            ->add('description', TextType::class, [
                'label' => $this->__('Description') . ':',
                'required' => false
            ])
        ;
    }

    public function getBlockPrefix()
    {
        return 'zikulacontentmodule_contenttype_quote';
    }
}
