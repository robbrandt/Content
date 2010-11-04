<?php

class Content_Form_Handler_Admin_Settings extends Form_Handler
{
    function initialize($view)
    {
        if (!SecurityUtil::checkPermission('Content::', '::', ACCESS_ADMIN)) {
            return $view->registerError(LogUtil::registerPermissionError());
        }
        $catoptions = array( array('text' => $this->__('Use 2 category levels (1st level single, 2nd level multi selection)'), 'value' => '1'),
                             array('text' => $this->__('Use 2 category levels (both single selection)'), 'value' => '2'),
                             array('text' => $this->__('Use 1 category level'), 'value' => '3'),
                             array('text' => $this->__("Don't use Categories at all"), 'value' => '4') );
                        
        $view->assign('catoptions', $catoptions);
        $view->assign('categoryusage', 1);
        // Assign all module vars
        $view->assign('config', ModUtil::getVar('Content'));

        return true;
    }

    function handleCommand($view, &$args)
    {
        $dom = ZLanguage::getModuleDomain('Content');
        if ($args['commandName'] == 'save') {
            if (!$view->isValid()) {
                return false;
            }

            $data = $view->getValues();

            if (!ModUtil::setVars('Content', $data['config'])) {
                return $view->setErrorMsg($this->__('Failed to set configuration variables'));
            }
            if ($data['config']['categoryUsage'] < 4) {
                // load the category registry util
                $mainCategory = CategoryRegistryUtil::getRegisteredModuleCategory('Content', 'content_page', $data['config']['categoryPropPrimary']);
                if (!$mainCategory) {
                    return LogUtil::registerError($this->__('Main category property does not exist.'));
                }
                if ($data['config']['categoryUsage'] < 3) {
                    $secondCategory = CategoryRegistryUtil::getRegisteredModuleCategory('Content', 'content_page', $data['config']['categoryPropSecondary']);
                    if (!$secondCategory) {
                        return LogUtil::registerError($this->__('Second category property does not exist.'));
                    }
                }
            }
            LogUtil::registerStatus($this->__('Done! Saved module configuration.'));
        } else if ($args['commandName'] == 'cancel') {
        }

        $url = ModUtil::url('Content', 'admin', 'main');

        return $view->redirect($url);
    }
}

