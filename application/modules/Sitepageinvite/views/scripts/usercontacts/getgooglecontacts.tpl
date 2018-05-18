<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageinvite
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: getgooglecontacts.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitepageinvite/externals/styles/style_sitepageinvite.css');
?>
<?php 		
if (!$this->errormessage) {
	if (empty($this->addtofriend)) { ?>
	<div id='show_nonsitefriends' style="display:block;">
	<?php
	} else { ?>
	<div id='show_nonsitefriends' style="display:none;">
	<?php
	}
	$total = count($this->addtononfriend);
	if ($total > 0) { ?>
		<div class="header">	
			<div class="title">	
				<?php echo $this->translate("Found %s Gmail contacts you can promote this Page to.", $total);?>
			</div>
			<div>
				<br /><?php echo $this->translate("Select the contacts to invite to your Page from the list below.");?>
			</div>
		</div>
	<?php  
		
	}
	if (!empty($this->addtononfriend)) { ?>
		<div class="member-friend-list">
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td width="5%">
						<input type="checkbox" name="nonsiteselect_all"  id="nonsiteselect_all" onclick="nonsitecheckedAll();" checked="checked">
					</td>
					<td colspan="2">
						<b><?php echo $this->translate("Select all");?></b>
					</td>
				</tr>
				<?php
					$total_contacts = 0;
					foreach($this->addtononfriend as $values) {
						$total_contacts++;?>
					<tr>
						<td width="5%">
							<input type="checkbox" name="nonsitecontactname_"<?php echo $total_contacts;?>  id="nonsitecontact_<?php echo $total_contacts;?>" checked="checked" value='<?php echo $values['contactMail'];?>'>
						</td>
						<td>
							<b><?php echo $values['contactName'];?></b>
						</td>
						<td>
							<?php echo $values['contactMail']; ?>
						</td>
					</tr>	
					
					<?php
					
					} ?>

				<input type="hidden" name="nonsitetotal_contacts"  id="nonsitetotal_contacts" value="<?php echo $total_contacts;?>"  onclick="nonsiteInviteFriends();">
			</table>
		</div>
		<?php  
			$getGooglePer = Zend_Registry::isRegistered('sitepageinvite_getGooglePer') ? Zend_Registry::get('sitepageinvite_getGooglePer') : null; 
			if( !empty($getGooglePer) ):
		?>
		<div class="buttons">
			<button name="invitefriends"  id="invitefriends" onclick="invitePreview();" class="sitepageinvite-send-button"><?php echo $this->translate("Send");?></button>
			<form action="" method="post" >	
				<?php echo $this->translate("or");  ?> <button class="disabled" name="skip_invite"  id="skip_invite"  type="submit"><?php echo $this->translate("Skip");?></button>
			</form>
		</div>
		<?php endif; ?>
	<?php
	} ?>
	</div>
<?php
}
else {
	echo "<div>" . $this->translate("All your imported contacts are already members of ")  . Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title') . ".</div>";
}
?>

<?php if (isset($this->redirectToOrigine)): ?>

    <script> 
      //if (window.opener!= null) {
    var access_token = window.location.href.split('#access_token='); 
    var redirect_href = '<?php echo $this->redirectToOrigine;?>'
    if (typeof access_token[1] != 'undefined' ) {
		  
		 redirect_href = '<?php echo $this->redirectToOrigine;?>' + '#access_token=' + access_token[1];
		}
    else if (typeof access_token[1] == 'undefined' ) { 
      var access_token = window.location.href.split('?token=');
		  //var token = window.opener.getQuerystring('token', window.location.href);
		  var redirect_href = redirect_href + '?token=' +  access_token[1];
		}
    
    
    window.location.href = redirect_href; 
   //}
      
      
    </script>
    
    <?php endif;?>