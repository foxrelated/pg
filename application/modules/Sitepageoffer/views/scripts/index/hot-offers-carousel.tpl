<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageoffer
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: hot-offers-carousel.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $request = Zend_Controller_Front::getInstance()->getRequest();
	$urlO = $request->getRequestUri();
	$request_url = explode('/',$urlO);
	$param = 1;
	if(empty($request_url['2'])) {
	$param = 0;
	}
	$return_url = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://":"http://";
	$currentUrl = urlencode($urlO);
?>

<?php if(!empty($this->viewer_id)):?>
	<?php $oldTz = date_default_timezone_get();?>
	<?php date_default_timezone_set($this->viewer->timezone);?>
<?php endif;?>

<?php if ($this->direction == 1) { ?>
 <?php  $j=0; $offset=$this->offset; ?>

    <?php foreach ($this->hotOffers as $offer): ?>
      <?php $sitepage_object = Engine_Api::_()->getItem('sitepage_page', $offer->page_id);?>
      <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
			$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepageoffer.profile-sitepageoffers', $offer->page_id, $layout); ?>
      <?php if($j% $this->itemsVisible ==0):?>
        <div class="Sitepagecontent_SlideItMoo_element Sitepageoffer_SlideItMoo_element" style="width:<?php echo 146 * $this->inOneRow; ?>px;">
        <div class="Sitepagecontent_SlideItMoo_contentList">
      <?php endif;?>
      <div class="featured_thumb_content">
				<?php if(!empty($offer->photo_id)):?>
					<a class="thumb_img" href="<?php echo $offer->getHref(array('route' => 'sitepageoffer_view', 'user_id' => $offer->owner_id, 'offer_id' =>  $offer->offer_id,'tab' => $tab_id,'slug' => $offer->getOfferSlug($offer->title))); ?>">
							<span style="background-image: url(<?php echo $offer->getPhotoUrl('thumb.normal'); ?>);"></span>
					</a>
				<?php else:?>
					<a class="thumb_img" href="<?php echo $offer->getHref(array( 'page_id' => $offer->page_id, 'offer_id' => $offer->offer_id,'slug' => $offer->getOfferSlug($offer->title), 'tab' => $tab_id)); ?>">
					<span style="background-image: url('<?php echo $this->layout()->staticBaseUrl . "application/modules/Sitepageoffer/externals/images/nophoto_offer_thumb_normal.png" ?>');"></span>
					</a>
				<?php endif;?>
			<span class="show_content_des">
				<?php
				$owner = $offer->getOwner();
				//$parent = $offer->getParent();
				echo $this->htmlLink($offer->getHref(), $this->string()->truncate($offer->getTitle(),25),array('title'=> $offer->getTitle()));
				?>
				<?php
				$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.title.truncation', 18);
				$tmpBody = strip_tags($sitepage_object->title);
				$page_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
				?>
				<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($offer->page_id, $offer->owner_id, $offer->getSlug()),  $page_title,array('title' => $sitepage_object->title)) ?> 
				<?php $today = date("Y-m-d H:i:s"); ?>
        <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepageoffer.getofferlink', 1)): ?>   
				<?php $claim_value = Engine_Api::_()->getDbTable('claims','sitepageoffer')->getClaimValue($this->viewer_id,$offer->offer_id,$offer->page_id);?>
				<?php if($offer->claim_count == -1 && ($offer->end_time > $today || $offer->end_settings == 0)):?>
					<?php $show_offer_claim = 1;?>
				<?php elseif($offer->claim_count > 0 && ($offer->end_time > $today || $offer->end_settings == 0)):?>
					<?php $show_offer_claim = 1;?>
				<?php else:?>
					<?php $show_offer_claim = 0;?>
				<?php endif;?>
			<div class="sitepage_offer_date seaocore_txt_light" style="margin:3px 0 0;">
				<?php if(!empty($show_offer_claim) && empty($claim_value)):?>
					<span>
						<?php if(!empty($this->viewer_id)):?>
							<?php echo '<img src="'.$this->layout()->staticBaseUrl.'application/modules/Sitepageoffer/externals/images/invite.png" alt="" class="get_offer_icon" />'.$this->htmlLink(array('route' => 'sitepageoffer_general', 'action' => 'getoffer', 'id' => $offer->offer_id),$this->translate('Get Offer'),array('class' => 'smoothbox'));
							?>
					  <?php else:?>
							<?php 
							$offer_tabinformation = $this->url(array( 'action' => 'getoffer', 'id' => $offer->offer_id,'param' => $param,'request_url'=>$request_url['1']), 'sitepageoffer_general')."?"."return_url=".$return_url.$_SERVER['HTTP_HOST'].$currentUrl;
							$title = $this->translate('Get Offer');
							echo '<img src="'.$this->layout()->staticBaseUrl.'application/modules/Sitepageoffer/externals/images/invite.png" alt="" class="get_offer_icon" />'."<a href=$offer_tabinformation>$title</a>";
							?>
					  <?php endif;?>
					</span>	
				<?php elseif(!empty($claim_value) && !empty($show_offer_claim) || ($offer->claim_count == 0 && $offer->end_time > $today && !empty($claim_value))):?>
					<span>
						<?php echo '<img src="'.$this->layout()->staticBaseUrl.'application/modules/Sitepageoffer/externals/images/invite.png" alt="" class="get_offer_icon" style="margin-top:1px;" />'.$this->htmlLink(array('route' => 'sitepageoffer_general', 'action' => 'resendoffer', 'id' => $offer->offer_id),Zend_Registry::get('Zend_Translate')->_('Resend Offer'),array('onclick' => 'owner(this);return false'));?>
					</span>	
				<?php else:?>
					<span>
						<b><?php echo $this->translate('Expired');?></b>
					</span>	
				<?php endif;?>
			</div> 
      <?php endif; ?>
			</span>
      </div>
        <?php $j++; $offset++;?>
       <?php if(($j% $this->itemsVisible) ==0):?>
           </div>
        </div>    
       <?php endif;?>     
    <?php endforeach; ?>
    <?php if($j <($this->totalItemsInSlide)):?>
       <?php for ($j;$j<($this->totalItemsInSlide); $j++ ): ?>
      <div class="featured_thumb_content">
      </div>
       <?php endfor; ?>
         </div>
      </div>
    <?php endif;?>
     
<?php } else {?>
<?php $count=$this->itemsVisible;
$j=0;  $offset=$this->offset+$count;?>
  <?php for ($i =$count; $i < $this->totalItemsInSlide; $i++):?> 
      <?php if ($j % $this->itemsVisible == 0): ?>
      <div class="Sitepagecontent_SlideItMoo_element Sitepageoffer_SlideItMoo_element" style="width:<?php echo 146 * $this->inOneRow; ?>px;">
        <div class="Sitepagecontent_SlideItMoo_contentList">
      <?php endif; ?>
          <?php if ($i < $this->count): ?>
            <div class="featured_thumb_content">
              <?php $sitepage_object = Engine_Api::_()->getItem('sitepage_page', $this->hotOffers[$i]->page_id);?>
              <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
							$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepageoffer.profile-sitepageoffers', $this->hotOffers[$i]->page_id, $layout); ?>
							<?php if(!empty($this->hotOffers[$i]->photo_id)):?>
								<a class="thumb_img" href="<?php echo $this->hotOffers[$i]->getHref(array('route' => 'sitepageoffer_view', 'user_id' => $this->hotOffers[$i]->owner_id, 'offer_id' =>  $this->hotOffers[$i]->offer_id,'tab' => $tab_id,'slug' => $this->hotOffers[$i]->getOfferSlug($this->hotOffers[$i]->title))); ?>">
										<span style="background-image: url(<?php echo $this->hotOffers[$i]->getPhotoUrl('thumb.normal'); ?>);"></span>
								</a>
							<?php else:?>
								<a class="thumb_img" href="<?php echo $this->hotOffers[$i]->getHref(array( 'page_id' => $this->hotOffers[$i]->page_id, 'offer_id' => $this->hotOffers[$i]->offer_id,'slug' => $this->hotOffers[$i]->getOfferSlug($this->hotOffers[$i]->title), 'tab' => $tab_id)); ?>">
								<span style="background-image: url('<?php echo $this->layout()->staticBaseUrl . "application/modules/Sitepageoffer/externals/images/nophoto_offer_thumb_normal.png" ?>');"></span>
								</a>
							<?php endif;?>
							<span class="show_content_des">
            		<?php
                $owner = $this->hotOffers[$i]->getOwner();
               // $parent = $this->hotOffers[$i]->getParent();
                echo
                     $this->htmlLink($this->hotOffers[$i]->getHref(), $this->string()->truncate($this->hotOffers[$i]->getTitle(),25),array('title'=> $this->hotOffers[$i]->getTitle()));
                ?>
								<?php
								$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.title.truncation', 18);
								$tmpBody = strip_tags($sitepage_object->title);
								$page_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
								?>
								<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($this->hotOffers[$i]->page_id, $this->hotOffers[$i]->owner_id, $this->hotOffers[$i]->getOfferSlug($this->hotOffers[$i]->title)),  $page_title,array('title' => $sitepage_object->title)) ?>
									<?php $today = date("Y-m-d H:i:s"); ?>
                  <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepageoffer.getofferlink', 1)): ?>
									<?php $claim_value = Engine_Api::_()->getDbTable('claims','sitepageoffer')->getClaimValue($this->viewer_id,$this->hotOffers[$i]->offer_id,$this->hotOffers[$i]->page_id);?>
									<?php if($this->hotOffers[$i]->claim_count == -1 && ($this->hotOffers[$i]->end_time > $today || $this->hotOffers[$i]->end_settings == 0)):?>
										<?php $show_offer_claim = 1;?>
									<?php elseif($this->hotOffers[$i]->claim_count > 0 && ($this->hotOffers[$i]->end_time > $today || $this->hotOffers[$i]->end_settings == 0)):?>
										<?php $show_offer_claim = 1;?>
									<?php else:?>
										<?php $show_offer_claim = 0;?>
									<?php endif;?>
								<div class="sitepage_offer_date seaocore_txt_light" style="margin:3px 0 0;">
									<?php if(!empty($show_offer_claim) && empty($claim_value)):?>
										<span>
											<?php if(!empty($this->viewer_id)):?>
												<?php echo '<img src="'.$this->layout()->staticBaseUrl.'application/modules/Sitepageoffer/externals/images/invite.png" alt="" class="get_offer_icon" />'.$this->htmlLink(array('route' => 'sitepageoffer_general', 'action' => 'getoffer', 'id' => $this->hotOffers[$i]->offer_id),$this->translate('Get Offer'),array('class' => 'smoothbox'));
												?>
											<?php else:?>
												<?php 
												$offer_tabinformation = $this->url(array( 'action' => 'getoffer', 'id' => $this->hotOffers[$i]->offer_id,'param' => $param,'request_url'=>$request_url['1']), 'sitepageoffer_general')."?"."return_url=".$return_url.$_SERVER['HTTP_HOST'].$currentUrl;
												$title = $this->translate('Get Offer');
												echo '<img src="'.$this->layout()->staticBaseUrl.'application/modules/Sitepageoffer/externals/images/invite.png" alt="" class="get_offer_icon" />'."<a href=$offer_tabinformation>$title</a>";
												?>
											<?php endif;?>
										</span>	
									<?php elseif(!empty($claim_value) && !empty($show_offer_claim) || ($this->hotOffers[$i]->claim_count == 0 && $this->hotOffers[$i]->end_time > $today && !empty($claim_value))):?>
										<span>
											<?php echo '<img src="'.$this->layout()->staticBaseUrl.'application/modules/Sitepageoffer/externals/images/invite.png" alt="" class="get_offer_icon" style="margin-top:1px;" />'.$this->htmlLink(array('route' => 'sitepageoffer_general', 'action' => 'resendoffer', 'id' => $this->hotOffers[$i]->offer_id),Zend_Registry::get('Zend_Translate')->_('Resend Offer'),array('onclick' => 'owner(this);return false'));?>
										</span>	
									<?php else:?>
										<span>
											<b><?php echo $this->translate('Expired');?></b>
										</span>	
									<?php endif;?>
								</div> 
                <?php endif; ?>
            	</span>
             </div>
          <?php else: ?>
             <div class="featured_thumb_content">
             </div>
          <?php endif; ?>
      <?php $j++; $offset++;?>
      <?php if (($j % $this->itemsVisible) == 0): ?>
          </div>
        </div>
      <?php endif; ?>     
     
  <?php endfor;?>
 <?php $j=0; $offset=$this->offset; ?>
 <?php for ($i = 0; $i < $count; $i++): ?>
   <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
			$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepageoffer.profile-sitepageoffers', $this->hotOffers[$i]->page_id, $layout); ?>
   <?php if ($j % $this->itemsVisible == 0): ?>
      <div class="Sitepagecontent_SlideItMoo_element Sitepageoffer_SlideItMoo_element" style="width:<?php echo 146 * $this->inOneRow; ?>px;">
        <div class="Sitepagecontent_SlideItMoo_contentList">
      <?php endif; ?>        
            <div class="featured_thumb_content">
							<?php $sitepage_object = Engine_Api::_()->getItem('sitepage_page', $this->hotOffers[$i]->page_id);?>
							<?php if(!empty($this->hotOffers[$i]->photo_id)):?>
								<a class="thumb_img" href="<?php echo $this->hotOffers[$i]->getHref(array('route' => 'sitepageoffer_view', 'user_id' => $this->hotOffers[$i]->owner_id, 'offer_id' =>  $this->hotOffers[$i]->offer_id,'tab' => $tab_id,'slug' => $this->hotOffers[$i]->getOfferSlug($this->hotOffers[$i]->title))); ?>">
										<span style="background-image: url(<?php echo $this->hotOffers[$i]->getPhotoUrl('thumb.normal'); ?>);"></span>
								</a>
							<?php else:?>
								<a class="thumb_img" href="<?php echo $this->hotOffers[$i]->getHref(array( 'page_id' => $this->hotOffers[$i]->page_id, 'offer_id' => $this->hotOffers[$i]->offer_id,'slug' => $this->hotOffers[$i]->getOfferSlug($this->hotOffers[$i]->title), 'tab' => $tab_id)); ?>">
								<span style="background-image: url('<?php echo $this->layout()->staticBaseUrl . "application/modules/Sitepageoffer/externals/images/nophoto_offer_thumb_normal.png" ?>');"></span>
								</a>
							<?php endif;?>
							<span class="show_content_des">
            		<?php
                $owner = $this->hotOffers[$i]->getOwner();
               // $parent = $this->hotOffers[$i]->getParent();
                echo
                     $this->htmlLink($this->hotOffers[$i]->getHref(), $this->string()->truncate($this->hotOffers[$i]->getTitle(),25),array('title'=> $this->hotOffers[$i]->getTitle()));
                ?>
								<?php
								$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.title.truncation', 18);
								$tmpBody = strip_tags($sitepage_object->title);
								$page_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
								?>
								<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($this->hotOffers[$i]->page_id, $this->hotOffers[$i]->owner_id, $this->hotOffers[$i]->getSlug()),  $page_title,array('title' => $sitepage_object->title)) ?>
                <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepageoffer.getofferlink', 1)): ?>  
								<?php $today = date("Y-m-d H:i:s"); ?>
								<?php $claim_value = Engine_Api::_()->getDbTable('claims','sitepageoffer')->getClaimValue($this->viewer_id,$this->hotOffers[$i]->offer_id,$this->hotOffers[$i]->page_id);?>
								<?php if($this->hotOffers[$i]->claim_count == -1 && ($this->hotOffers[$i]->end_time > $today || $this->hotOffers[$i]->end_settings == 0)):?>
									<?php $show_offer_claim = 1;?>
								<?php elseif($this->hotOffers[$i]->claim_count > 0 && ($this->hotOffers[$i]->end_time > $today || $this->hotOffers[$i]->end_settings == 0)):?>
									<?php $show_offer_claim = 1;?>
								<?php else:?>
									<?php $show_offer_claim = 0;?>
								<?php endif;?>
								<div class="sitepage_offer_date seaocore_txt_light" style="margin:3px 0 0;">
									<?php if(!empty($show_offer_claim) && empty($claim_value)):?>
										<span>
											<?php if(!empty($this->viewer_id)):?>
												<?php echo '<img src="'.$this->layout()->staticBaseUrl.'application/modules/Sitepageoffer/externals/images/invite.png" alt="" class="get_offer_icon" />'.$this->htmlLink(array('route' => 'sitepageoffer_general', 'action' => 'getoffer', 'id' => $this->hotOffers[$i]->offer_id),$this->translate('Get Offer'),array('class' => 'smoothbox'));
												?>
											<?php else:?>
												<?php 
												$offer_tabinformation = $this->url(array( 'action' => 'getoffer', 'id' => $this->hotOffers[$i]->offer_id,'param' => $param,'request_url'=>$request_url['1']), 'sitepageoffer_general')."?"."return_url=".$return_url.$_SERVER['HTTP_HOST'].$currentUrl;
												$title = $this->translate('Get Offer');
												echo '<img src="'.$this->layout()->staticBaseUrl.'application/modules/Sitepageoffer/externals/images/invite.png" alt="" class="get_offer_icon" />'."<a href=$offer_tabinformation>$title</a>";
												?>
											<?php endif;?>
										</span>	
									<?php elseif(!empty($claim_value) && !empty($show_offer_claim) || ($this->hotOffers[$i]->claim_count == 0 && $this->hotOffers[$i]->end_time > $today && !empty($claim_value))):?>
										<span>
											<?php echo '<img src="'.$this->layout()->staticBaseUrl.'application/modules/Sitepageoffer/externals/images/invite.png" alt="" class="get_offer_icon" style="margin-top:1px;" />'.$this->htmlLink(array('route' => 'sitepageoffer_general', 'action' => 'resendoffer', 'id' => $this->hotOffers[$i]->offer_id),Zend_Registry::get('Zend_Translate')->_('Resend Offer'),array('onclick' => 'owner(this);return false'));?>
										</span>	
									<?php else:?>
										<span>
											<b><?php echo $this->translate('Expired');?></b>
										</span>	
									<?php endif;?>
								</div> 
                <?php endif;?>  
            	</span>
	          </div>
         <?php $j++; $offset++; ?>
        <?php if ($j % $this->itemsVisible == 0): ?>
          </div>
        </div>
      <?php endif; ?>
  <?php endfor; ?>
 <?php } ?>

<?php if(!empty($this->viewer_id)):?>
	<?php date_default_timezone_set($oldTz);?>
<?php endif;?>
 
<script type="text/javascript" >
	function owner(thisobj) {
		var Obj_Url = thisobj.href ;
		Smoothbox.open(Obj_Url);
	}
</script>
