<?php 
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageurl
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Addurl.tpl 2010-07-08 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>

<?php if( @$this->closeSmoothbox ): ?>
	<script type="text/javascript">
  		TB_close();
	</script>
<?php endif; ?>
<style type="text/css">
#module_name-label label{
	font-weight:bold;
}
#module_name-element select{
margin:3px 0 10px;
}
</style>