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
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zikula\Common\Content\AbstractContentFormType;

/**
 * Feed form type class.
 */
class FeedType extends AbstractContentFormType
{
    public function __construct(TranslatorInterface $translator)
    {
        $this->setTranslator($translator);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('url', UrlType::class, [
                'label' => $this->__('URL of RSS or Atom feed') . ':'
            ])
            ->add('includeContent', CheckboxType::class, [
                'label' => $this->__('Include feed text in addition to the title') . ':',
                'required' => false
            ])
            ->add('refreshTime', IntegerType::class, [
                'label' => $this->__('Refresh time') . ':',
                'input_group' => ['right' => $this->__('hours')]
            ])
            ->add('maxNoOfItems', IntegerType::class, [
                'label' => $this->__('Max. no. of items to display') . ':'
            ])
        ;
    }

    public function getBlockPrefix()
    {
        return 'zikulacontentmodule_contenttype_feed';
    }
}
