<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitereaction
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: FormFancyUpload.php 6590 2016-07-07 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitereaction_Form_Decorator_FormFancyUpload extends Engine_Form_Decorator_FormFancyUpload {
  public function render($content) {
    $data = $this->getElement()->getAttrib('data');
    if ($data) {
      $this->getElement()->setAttrib('data', null);
    }
    $view = $this->getElement()->getView();
    return $view->partial('upload/upload.tpl', 'sitereaction', array(
        'name' => $this->getElement()->getName(),
        'data' => $data,
        'element' => $this->getElement()
    ));
  }

}
