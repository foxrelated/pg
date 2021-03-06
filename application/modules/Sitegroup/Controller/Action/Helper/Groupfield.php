<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Groupfield.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Controller_Action_Helper_Groupfield extends Zend_Controller_Action_Helper_Abstract {

  function postDispatch() {
  
    //GET NAME OF MODULE, CONTROLLER AND ACTION
    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();
    $controller = $front->getRequest()->getControllerName();
    $action = $front->getRequest()->getActionName();
    $view = $this->getActionController()->view;

    //ADD GROUP PRIVACY FIELDS AT FIELD CREATION AND EDITION
    if (($module == 'sitegroup') && ($action == 'field-create' || $action == 'heading-edit' || $action == 'field-edit') && ($controller == 'admin-fields')) {
    
      $new_element = $view->form;
      if (!$this->getRequest()->isPost() || (isset($view->form) && (!$view->form->isValid($this->getRequest()->getPost())))) {
      
        $new_element->addElement('Select', 'browse', array(
            'label' => 'SHOW ON BROWSE GROUP?',
            'multiOptions' => array(
                1 => 'Show in such Widgets',
                0 => 'Hide in such Widgets'
            )
        ));
        
        if ($front->getRequest()->getParam('field_id')) {
          $field = Engine_Api::_()->fields()->getField($front->getRequest()->getParam('field_id'), 'sitegroup_group');
          $new_element->browse->setValue($field->browse);
        }
        $new_element->buttons->setOrder(1000);
      } else {
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->update('engine4_sitegroup_group_fields_meta', array('browse' => $_POST['browse']), array('field_id = ?' => $view->field['field_id']));
      }
    }
  }
}