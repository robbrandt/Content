<?php
/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <vorstand@zikula.de>.
 * @link https://zikula.de
 * @link https://ziku.la
 * @version Generated by ModuleStudio 1.3.2 (https://modulestudio.de).
 */

namespace Zikula\ContentModule\Form\EventListener\Base;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

/**
 * Translation listener base class.
 *
 * Based on https://github.com/a2lix/TranslationFormBundle/blob/master/Form/EventListener/TranslationsListener.php
 */
abstract class AbstractTranslationListener implements EventSubscriberInterface
{
    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData'
        ];
    }
    
    /**
     * Adds translation fields to the form.
     *
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $formOptions = $form->getConfig()->getOptions();
    
        $entityForm = $this->getEntityForm($form->getParent());
    
        foreach ($formOptions['fields'] as $fieldName) {
            if (!$entityForm->has($fieldName)) {
                continue;
            }
    
            $originalFieldConfig = $entityForm->get($fieldName)->getConfig();
            $fieldOptions = $originalFieldConfig->getOptions();
            $fieldOptions['required'] = $fieldOptions['required'] && in_array($fieldName, $formOptions['mandatory_fields']);
            $fieldOptions['data'] = isset($formOptions['values'][$fieldName]) ? $formOptions['values'][$fieldName] : null;
    
            $form->add($fieldName, get_class($originalFieldConfig->getType()->getInnerType()), $fieldOptions);
        }
    }
    
    /**
     * Returns parent form editing the entity.
     *
     * @param FormInterface $form
     *
     * @return FormInterface
     */
    protected function getEntityForm(FormInterface $form)
    {
        $parentForm = $form;
        do {
            $parentForm = $form;
        } while ($form->getConfig()->getInheritData() && ($form = $form->getParent()));
    
        return $parentForm;
    }
}
