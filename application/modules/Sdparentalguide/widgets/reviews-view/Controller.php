<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sdparentalguide
 * @author     Stars Developer
 */

class Sdparentalguide_Widget_ReviewsViewController
  extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    $headLink = new Zend_View_Helper_HeadLink();
    $headScript = new Zend_View_Helper_HeadScript();
    $headLink->prependStylesheet('/styles/reviews_view.bundle.css');
    $headScript->prependFile('/scripts/reviews_view.bundle.js');
  }
}
