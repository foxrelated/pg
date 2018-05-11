<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagereview
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: delete.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
  include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/Adintegration.tpl';
?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>
<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adreviewdelete', 3) && $page_communityad_integration && Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepage)):?>
	<div class="layout_right" id="communityad_reviewdelete">

		<?php
					echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adreviewdelete', 3),"loaded_by_ajax"=>0,'widgetId'=>"page_reviewdelete")); 			 
				?>
	</div>
<?php endif;?>
<div class="layout_middle">
	<div class="sitepage_viewpages_head">
		<?php echo $this->htmlLink($this->sitepage->getHref(), $this->itemPhoto($this->sitepage, 'thumb.icon', '', array('align' => 'left'))) ?>
		<h2>	
		  <?php echo $this->sitepage->__toString() ?>	
		  <?php echo $this->translate('&raquo; ');?>
		  <?php echo $this->htmlLink($this->sitepage->getHref(array('tab'=>$this->tab_selected_id)), $this->translate('Reviews')) ?>
		</h2>
	</div>
	<form method="post" class="global_form">
		<div>
			<div>
				<h3><?php echo $this->translate('Delete Review?'); ?></h3>
				<p>
					<?php echo $this->translate('Are you sure that you want to delete this Page review? It will not be recoverable after being deleted.'); ?>
				</p>
				<br />
				<p>
					<input type="hidden" name="confirm" value="true"/>
					<button type='submit'><?php echo $this->translate('Delete'); ?></button>
					<?php echo $this->translate('or');?> <?php echo $this->htmlLink($this->sitepage->getHref(array('tab'=>$this->tab_selected_id)), $this->translate('Cancel')) ?>
				</p>
			</div>
		</div>
	</form>
</div>