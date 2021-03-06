<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemusic
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitepagemusic_Widget_FeaturedMusicsCarouselController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    //SEARCH PARAMETER
    $params = array();
    $params['feature_musics'] = 1;
    $this->view->category_id = $params['category_id'] = $this->_getParam('category_id',0);
    $this->view->featuredMusics = $featuredMusics = Engine_Api::_()->getDbTable('playlists', 'sitepagemusic')->widgetMusicList($params);
    $this->view->totalCount_music = count($featuredMusics);
    if (!($this->view->totalCount_music > 0)) {
      return $this->setNoRender();
    }

    $this->view->inOneRow_music = $inOneRow = $this->_getParam('inOneRow', 3);
    $this->view->noOfRow_music = $noOfRow = $this->_getParam('noOfRow', 2);
    $this->view->totalItemShowmusic = $totalItemShow = $inOneRow * $noOfRow;
    $params['limit'] = $totalItemShow;
    // List List featured
    $this->view->featuredMusics = $this->view->featuredMusics = $featuredMusics = Engine_Api::_()->getDbTable('playlists', 'sitepagemusic')->widgetMusicList($params);

    // CAROUSEL SETTINGS  
    $this->view->interval = $interval = $this->_getParam('interval', 250);
    $this->view->count = $count = $featuredMusics->count();
    $this->view->heightRow = @ceil($count / $inOneRow);
    $this->view->vertical = $this->_getParam('vertical', 0);
  }

}