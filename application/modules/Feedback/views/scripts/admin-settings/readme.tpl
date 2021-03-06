<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	   Grouppoll
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	   http://www.socialengineaddons.com/license/
 * @version	   $Id: readme.tpl 2010-10-25 9:40:21Z SocialEngineAddOns $
 * @author 	   SocialEngineAddOns
 */
?>

<h2><?php echo $this->translate('Feedback Plugin') ?></h2>
<div class="tabs">
	<ul class="navigation">
		<li class="active">
			<a href="<?php echo $this->baseUrl() .'/admin/feedback/settings/readme'?>" ><?php echo $this->translate('Please go through these important points and proceed by clicking the button at the bottom of this page.') ?></a>
    </li>
	</ul>
</div>

<script type="text/javascript">
  function faq_show(id) {
    if($(id).style.display == 'block') {
      $(id).style.display = 'none';
    } else {
      $(id).style.display = 'block';
    }
  }
</script>

<div class="admin_files_wrapper" style="width:70%;">
	<ul class="admin_files feedback_faq" style="max-height:2500px;">	 
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("Q.The CSS of this plugin is not coming on my site. What should I do ?");?></a>
			<div class='faq' style='display: none;' id='faq_1'>
				<?php echo $this->translate("Please enable the 'Development Mode' system mode for your site from the Admin homepage and then check the page which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'.");?>
			</div>
		</li>
	</ul>
</div>
<br />
<button onclick="form_submit();"><?php echo $this->translate('Proceed to enter License Key') ?> </button>
<style type="text/css">
	.feedback_faq li div{
		clear:both;
		border-left:3px solid #ccc;
		padding:5px 5px 5px 10px;
		margin:5px;
		font-family:arial;
		line:height:18px; 
	}
</style>
<script type="text/javascript" >
	function form_submit() {
		var url='<?php echo $this->url(array('module' => 'feedback', 'controller' => 'settings'), 'admin_default', true) ?>';
		window.location.href=url;
	}
</script>