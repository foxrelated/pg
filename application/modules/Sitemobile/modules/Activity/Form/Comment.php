<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Comment.php 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitemobile_modules_Activity_Form_Comment extends Engine_Form {

    public function init() {
        $this->clearDecorators()
                ->addDecorator('FormElements')
                ->addDecorator('Form')
                ->setAttrib('class', null)
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                            'module' => 'activity',
                            'controller' => 'index',
                            'action' => 'comment',
                                ), 'default'));

        //$allowed_html = Engine_Api::_()->getApi('settings', 'core')->core_general_commenthtml;
        $viewer = Engine_Api::_()->user()->getViewer();
        $allowed_html = "";
        if ($viewer->getIdentity()) {
            $allowed_html = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $viewer->level_id, 'commentHtml');
        }

       // $this->addElement('File', 'photo', array(
           // 'label' => 'Choose Photo',
            //'onchange' => 'sm4.activity.composer.photo.doRequest(this)'
      //  ));
        //$this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');
        $this->addElement('Textarea', 'body', array(
            'rows' => 1,
            'data-role' => 'none',
            'class' => 'ui-input-field',
            'decorators' => array(
                'ViewHelper'
            ),
            'filters' => array(
                new Engine_Filter_Html(array('AllowedTags' => $allowed_html)),
                //new Engine_Filter_HtmlSpecialChars(),
                //new Engine_Filter_EnableLinks(),
                new Engine_Filter_Censor(),
            ),
        ));

//    if (Engine_Api::_()->getApi('settings', 'core')->core_spam_comment) {
//      $this->addElement('captcha', 'captcha', Engine_Api::_()->core()->getCaptchaOptions());
//    }

        $this->addElement('Hidden', 'show_all_comments', array(
            'value' => Zend_Controller_Front::getInstance()->getRequest()->getParam('show_comments'),
        ));

        $this->addElement('Button', 'submit', array(
            'type' => 'submit',
            'data-role' => 'none',
            'class' => 'ui-btn-default ui-btn-action',
            'ignore' => true,
            'label' => 'Post',
            'decorators' => array(
                'ViewHelper',
            )
        ));

        $this->addElement('Hidden', 'action_id', array(
            'order' => 990,
            'filters' => array(
                'Int'
            ),
        ));

        $this->addElement('Hidden', 'return_url', array(
            'order' => 991,
            'value' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array())
        ));
    }

    public function setActionIdentity($action_id) {
        if (0) {
            $this
                    ->setAttrib('style', 'display: none;');
        } else {
            $this
                    ->setAttrib('id', 'activity-comment-form-' . $action_id)
                    ->setAttrib('class', 'activity-comment-form')
                    ->setAttrib('style', 'display: none;');
        }
        $this->action_id
                ->setValue($action_id)
                ->setAttrib('id', 'activity-comment-id-' . $action_id);
        $this->submit //->getDecorator('HtmlTag')
                ->setAttrib('id', 'activity-comment-submit-' . $action_id)
        ;

        $this->body
                ->setAttrib('id', 'activity-comment-body-' . $action_id)
        ;
        //->setAttrib('onfocus', "document.getElementById('activity-comment-submit-".$action_id."').style.display = 'block';")
        //->setAttrib('onblur', "if( this.value == '' ) { document.getElementById('activity-comment-form-".$action_id."').style.display = 'none'; }");

        return $this;
    }

    public function renderFor($action_id) {
        return $this->setActionIdentity($action_id)->render();
    }

}