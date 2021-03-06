<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: PostController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_PostController extends Seaocore_Controller_Action_Standard {

  public function init() {

  	//IF THERE IS NO SUBJECT THEN RETURN
    if (Engine_Api::_()->core()->hasSubject())
      return;

    if (0 != ($post_id = (int) $this->_getParam('post_id')) &&
        null != ($post = Engine_Api::_()->getItem('sitegroup_post', $post_id))) {
      Engine_Api::_()->core()->setSubject($post);
    } else if (0 != ($topic_id = (int) $this->_getParam('topic_id')) &&
        null != ($topic = Engine_Api::_()->getItem('sitegroup_topic', $topic_id))) {
      Engine_Api::_()->core()->setSubject($topic);
    }
    $this->_helper->requireUser->addActionRequires(array(
            'edit',
            'delete',
    ));

    $this->_helper->requireSubject->setActionRequireTypes(array(
            'edit' => 'sitegroup_post',
            'delete' => 'sitegroup_post',
    ));
  }

  //ACTION FOR EDIT THE POST
  public function editAction() {

  	//CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET POST SUBJECT
    $post = Engine_Api::_()->core()->getSubject('sitegroup_post');

    //GET SITEGROUP SUBJECT
    $sitegroup = $post->getParent('sitegroup_group');

    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupdiscussion")) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    } else {
      $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'sdicreate');
      if (empty($isGroupOwnerAllow)) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    }
    //PACKAGE BASE PRIYACY END

    //GET TOPIC ITEM
    $topic = Engine_Api::_()->getItem('sitegroup_topic', $post->topic_id);

    //START MANAGE-ADMIN CHECK
    $can_edit = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if ($can_edit != 1 && !$post->isOwner(Engine_Api::_()->user()->getViewer()) ) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
		//END MANAGE-ADMIN CHECK

		//GET POST EDIT FORM
    $this->view->form = $form = new Sitegroup_Form_Post_Edit();

    //CHECK FORM VALIDATION
    if (!$this->getRequest()->isPost()) {
      $form->populate($post->toArray());
      return;
    }

    //CHECK FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //PROCESS
    $db = $post->getTable()->getAdapter();
    $db->beginTransaction();
    try {
    	//SAVE POST
      $post->setFromArray($form->getValues());
      $post->modified_date = date('Y-m-d H:i:s');
      $post->save();

      //COMMIT
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    //REDIRECTING
    return $this->_forwardCustom('success', 'utility', 'core', array(
            'closeSmoothbox' => true,
            'parentRefresh' => true,
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('The changes to your post have been saved.')),
    ));
  }

  //ACTION FOR DELETE THE POST
  public function deleteAction() {

  	//CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET POST SUBJECT
    $post = Engine_Api::_()->core()->getSubject('sitegroup_post');

    //GET SITEGROUP SUBJECT
    $sitegroup = $post->getParent('sitegroup_group');

    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupdiscussion")) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    } else {
      $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'sdicreate');
      if (empty($isGroupOwnerAllow)) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    }
    //PACKAGE BASE PRIYACY END

    //GET TOPIC ITEM
    $topic = Engine_Api::_()->getItem('sitegroup_topic', $post->topic_id);

    //START MANAGE-ADMIN CHECK
    $can_edit = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if ($can_edit != 1 && Engine_Api::_()->user()->getViewer()->getIdentity() != $post->user_id && Engine_Api::_()->user()->getViewer()->getIdentity() != $topic->user_id) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK

    //MAKE FORM
    $this->view->form = $form = new Sitegroup_Form_Post_Delete();

    //CHECK FORM VALIDATION
    if (!$this->getRequest()->isPost()) {
      return;
    }

    //CHECK FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //PROCESS
    $db = $post->getTable()->getAdapter();
    $db->beginTransaction();
    try {
    	//GET TOPIC ID
    	$topic_id = $post->topic_id;
    	//DELETE POST
      $post->delete();

      //COMMIT
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    //TRY TO GET TOPIC
    $topic = Engine_Api::_()->getItem('sitegroup_topic', $topic_id);
    $href = ( null == $topic ? $sitegroup->getHref() : $topic->getHref() );
    return $this->_forwardCustom('success', 'utility', 'core', array(
            'closeSmoothbox' => true,
            'parentRedirect' => $href,
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('The selected post has been deleted.')),
    ));
  }
}

?>