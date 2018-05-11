<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: likesitegroup.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/common_style_css.tpl';
?>
<?php $paginater_vari = 0; if( !empty($this->user_obj)) {  $paginater_vari = $this->user_obj->getCurrentPageNumber(); }  ?>


<script type="text/javascript">
	// Function for Searching.
	var likeMemberGroup = <?php if(empty($this->no_result_msg)){ echo sprintf('%d', $paginater_vari); } else { echo 1; } ?>;
	var call_status = '<?php echo $this->call_status; ?>';
	var resource_id = '<?php echo $this->resource_id; ?>';// Resource Id which are send to controller in the 'pagination' & 'searching'.
	var resource_type = '<?php echo $this->resource_type; ?>';// Resource Type which are send to controller in the 'pagination' & 'searching'.
	
	//var url = en4.core.baseUrl + 'sitegroup/index/likesitegroup';// URL where send ajax request.
	var url = '<?php echo $this->url(array('action' => 'like-groups'), 'sitegroup_like', true);?>';
	function show_myfriend () {
		$('sitegroups_popup_content').innerHTML = '<center><img src="application/modules/sitegroup/externals/images/spinner.gif" /></center>';
      en4.core.request.send(new Request.HTML({
        'url' : url,
        'data' : {
          'format' : 'html',
					'resource_type' : resource_type,
					'resource_id' : resource_id,
					'call_status' : call_status,
          'search' : this.value,
					'is_ajax':1
        },
				onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
				$('sitegroups_popup_content').innerHTML = responseHTML;
				}
      }));
  }

  en4.core.runonce.add(function() {
		// Code for 'searching', where send the request and set the result which are return.
    $('like_members_search_input').addEvent('keyup', function(e) {
		$('sitegroups_popup_content').innerHTML = '<center><img src="application/modules/Sitegroup/externals/images/spinner.gif" alt="" class="mtop10" /></center>';
      en4.core.request.send(new Request.HTML({
        'url' : url,
        'data' : {
          'format' : 'html',
					'resource_type' : resource_type,
					'resource_id' : resource_id,
					'call_status' : call_status,
          'search' : this.value,
					'is_ajax':1
        },
				onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
				$('sitegroups_popup_content').innerHTML = responseHTML;
				}
      }));
    });
  });

	// Code for 'Pagination' which decide that how many entry will show in popup.
	var paginateLikeMembers = function(group, call_status) {
		var search_value = $('like_members_search_input').value;
		if (search_value == 'Search Members') {
			search_value = '';
		}
    en4.core.request.send(new Request.HTML({
      'url' : url,
      'data' : {
        'format' : 'html',
				'resource_type' : resource_type,
				'resource_id' : resource_id,
        'search' : search_value,
				'call_status' : call_status,
        'group' : group,
				'is_ajax':1
      },
			onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
			$('sitegroups_popup_content').innerHTML = responseHTML;
			}
    }), {
      //'element' : $('likes_popup_content')
    });
  }

	//Showing 'friend' which liked this content.
	var likedStatus = function(call_status) {
    en4.core.request.send(new Request.HTML({
      'url' : url,
      'data' : {
        'format' : 'html',
				'resource_type' : resource_type,
				'resource_id' : resource_id,
				'call_status' : call_status
      }
    }), {
      'element' : $('like_members_profile').getParent()			
    });
  }
</script>
	<?php  if(empty($this->is_ajax)) { ?>
	<a id="like_members_profile"></a>
	<div class="seaocore_members_popup">
		<div class="top">
			<?php 
				if($this->resource_type == 'member') {
					$module_name = 'profile';
				} 
				elseif($this->resource_type == 'sitegroup_group') {
					$module_name = 'sitegroup';
				} 
				else {
					$module_name = $this->resource_type;
				}
				if($this->call_status == 'public') {
					$title = $this->translate('People Who Like This Group') ;
				}else {
					$title = $this->translate('Friends Who Like This Group') ;
				} 
			?>
			<div class="heading"><?php echo $title; ?></div>
			<div class="seaocore_members_search_box">
				<div class="link">
					<a href="javascript:void(0);" class="<?php if($this->call_status == 'public') { echo 'selected'; } ?>" id="show_all" onclick="likedStatus('public');"><?php echo $this->translate('All'); ?>(<?php echo number_format($this->public_count); ?>)</a>
					<a href="javascript:void(0);" class="<?php if($this->call_status == 'friend') { echo 'selected'; } ?>" onclick="likedStatus('friend');"><?php echo $this->translate('Friends'); ?>(<?php echo number_format($this->friend_count); ?>)</a>
				</div>
				<div class="seaocore_members_search fright">
					<input id="like_members_search_input" type="text" value="<?php echo $this->search; ?>" onfocus="if(this.value=='Search Members')this.value='';" onblur="if(this.value=='')this.value='Search Members';"/>
				</div>
				<div class="clear"></div>
			</div>
		</div>
		<div class="seaocore_members_popup_content" id="sitegroups_popup_content">
		<?php } ?>
		<?php if( !empty($this->user_obj) && $this->user_obj->count() > 1 ): ?>
			<?php if( $this->user_obj->getCurrentPageNumber() > 1 ): ?>
				<div class="seaocore_members_popup_paging">
					<div id="user_like_members_previous" class="paginator_previous">
						<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array('onclick' => 'paginateLikeMembers(likeMemberGroup - 1, call_status)'
						)); ?>
					</div>
				</div>
			<?php endif; ?>
		<?php  endif; ?>
		<?php $count_user = count($this->user_obj);
		if(!empty($count_user)) {
			foreach( $this->user_obj as $user_info ) {?>
				<div class="item_member">
					<div class="item_member_thumb">
						<?php echo $this->htmlLink($user_info->getHref(), $this->itemPhoto($user_info, 'thumb.icon', $user_info->getTitle()), array('class' => 'item_photo', 'target' => '_parent', 'title' => $user_info->getTitle()));?>
					</div>
					<div class="item_member_name">
						<?php  $title1 = $user_info->getTitle(); ?>
						<?php  $truncatetitle = Engine_String::strlen($title1) > 20 ? Engine_String::substr($title1, 0, 20) . '..' : $title1?>
						<?php echo $this->htmlLink($user_info->getHref(), $truncatetitle, array('title' => $user_info->getTitle())); ?>
						<?php //echo $this->htmlLink($user_info->getHref(), $truncatetitle, array('title' => $user_info->getTitle(), 'target' => '_parent'));?>
					</div>
				</div>
			<?php	}
		} 
		else { ?>
			<div class='tip'>
				<span>
					<?php echo $this->no_result_msg; ?>
				</span>
			</div>
		<?php } ?>
		<?php
			if(!empty($this->user_obj) && $this->user_obj->count() > 1 ): ?>
				<?php if( $this->user_obj->getCurrentPageNumber() < $this->user_obj->count() ): ?>
					<div class="seaocore_members_popup_paging">
						<div id="user_like_members_next" class="paginator_next" style="border-top-width:1px;">
							<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next') , array('onclick' => 'paginateLikeMembers(likeMemberGroup + 1, call_status)')); ?>
						</div>
					</div>
				<?php endif; ?>
			<?php endif; ?>
			<?php  if(empty($this->is_ajax)) { ?>
		</div>
	</div>
	<div class="seaocore_members_popup_bottom">
	<button onclick="parent.Smoothbox.close();"><?php echo $this->translate("Close") ?></button>
	</div>	
	<?php } ?>