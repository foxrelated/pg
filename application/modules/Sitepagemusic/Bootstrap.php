<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemusic
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Bootstrap.php 6590 2010-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagemusic_Bootstrap extends Engine_Application_Bootstrap_Abstract {

  protected function _initFrontController() {
    include APPLICATION_PATH . '/application/modules/Sitepagemusic/controllers/license/license.php';
  }
}

?>