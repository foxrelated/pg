<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupalbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroupalbum_Widget_ListPopularAlbumsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
  
    $category_id = $this->_getParam('category_id',0);
    // Should we consider views or comments popular?
    $popularType = $this->_getParam('popularType', 'comment');
    if (!in_array($popularType, array('comment', 'view', 'like'))) {
      $popularType = 'comment';
    }
    $this->view->popularType = $popularType;
    $this->view->popularCol = $popularCol = $popularType . '_count';
    //$popularAlbum = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.badgeviewer', null);
    // Get paginator
    $tableGroup = Engine_Api::_()->getDbtable('groups', 'sitegroup');
    $tableGroupName = $tableGroup->info('name');
    $table = Engine_Api::_()->getItemTable('sitegroup_album');
    $tableName = $table->info('name');
    $select = $table->select()
                    ->setIntegrityCheck(false)
                    ->from($tableName)
                    ->joinLeft($tableGroupName, "$tableGroupName.group_id = $tableName.group_id", array('title AS group_title', 'photo_id as group_photo_id'))
                    ->order($popularCol . ' DESC');
    $select = $select
              ->where($tableGroupName . '.closed = ?', '0')
              ->where($tableGroupName . '.approved = ?', '1')
              ->where($tableGroupName . '.declined = ?', '0')
              ->where($tableGroupName . '.draft = ?', '1');

    if(!empty($category_id)) {
      $select->where($tableGroupName . '.	category_id =?', $category_id);
    }
    
    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
      $select->where($tableGroupName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    }  
    
    if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupalbum.hide.autogenerated', 1) ) {
			$select->where($tableName. '.default_value'.'= ?', 0);
			$select->where($tableName . ".type is Null");
    }     

    //if (!Engine_Api::_()->sitealbum()->canShowSpecialAlbum())
      //$select->where('type IS NULL');
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);

    // Set item count per group and current group number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerGroup', 4));
    $paginator->setCurrentPageNumber($this->_getParam('group', 1));

    // Do not render if nothing to show
    if (($paginator->getTotalItemCount() <= 0)) {
      return $this->setNoRender();
    }
  }

}