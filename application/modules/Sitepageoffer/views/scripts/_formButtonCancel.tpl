<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageoffer
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formButtonCancel.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<button type="submit" id="done" name="done" >
  <?php echo ( $this->element->getLabel() ? $this->element->getLabel() : $this->translate('Save Changes')) ?>
</button>
<?php echo $this->translate(" or "); ?> 
<a onclick="javascript:parent.Smoothbox.close();" href="javascript:void(0);" type="button" id="cancel" name="cancel"><?php echo $this->translate('cancel'); ?></a>
