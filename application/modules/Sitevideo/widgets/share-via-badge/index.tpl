<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<a href="<?php echo $this->url(array('action' => 'create','channel_id'=>$this->channel->channel_id), 'sitevideo_badge', true) ?>" class="button seaocore_icon_share"><?php echo $this->translate("Share via Badge") ?>
</a>
