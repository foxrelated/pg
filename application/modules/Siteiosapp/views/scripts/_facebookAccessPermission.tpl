<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteandroidapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    _facebookAccessPermission.tpl 2015-10-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$isSelected = false;
$getWebsiteName = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
$websiteStr = str_replace(".", "-", $getWebsiteName);
$dirName = 'ios-' . $websiteStr . '-app-builder';
$appBuilderBaseFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/' . $dirName . '/settings.php';
if (file_exists($appBuilderBaseFile)) {
    include $appBuilderBaseFile;
    if (isset($appBuilderParams['facebook_access_permission']) && !empty($appBuilderParams['facebook_access_permission']))
        $isSelected = true;
}
?>

<div class="form-wrapper" id="facebook_access_permission-wrapper">
    <div id="facebook_access_permission-label" class="form-label">
        Give Permission to Access Your Facebook Developer Account
    </div>
    <div class="form-element" id="facebook_access_permission-element">
        <input type="hidden" value="" name="facebook_access_permission">
        <?php if (!empty($isSelected)): ?>
<!--            <input type="checkbox" onchange="showHideFacebookDisplayNameTextBox();" value="1" id="facebook_access_permission" name="facebook_access_permission" checked="checked">-->
        <input type="checkbox" value="1" id="facebook_access_permission" name="facebook_access_permission" checked="checked">
        <?php else: ?>
<!--            <input type="checkbox" onchange="showHideFacebookDisplayNameTextBox();" value="1" id="facebook_access_permission" name="facebook_access_permission">-->
            <input type="checkbox" value="1" id="facebook_access_permission" name="facebook_access_permission">
        <?php endif; ?>      

        <label class="required" for="facebook_access_permission">
            To configure Facebook login feature in your app, please add our "Developer" account with your Facebook Application. Our Developer Facebook Id is : <span style="font-weight: bold;">Nira010384</span>. Please follow <a href="https://youtu.be/vRuRwsE960U" target="_blank">Video Tutorial</a> for this.            
        </label>
    </div>
</div>

<script type="text/javascript">
//     window.addEvent('domready', function () {
//         if($('facebook_app_id').checked)
//            showHideFacebookDisplayNameTextBox();
//     });
//     
//    function showHideFacebookDisplayNameTextBox() {
//        if($('facebook_access_permission').checked){
//            $('facebook_app_display_name-wrapper').style.display = 'none';
//        }else {
//            $('facebook_app_display_name-wrapper').style.display = 'block';
//        }
//    }
</script>