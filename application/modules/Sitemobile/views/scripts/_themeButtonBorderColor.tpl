<?php 
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _themeButtonBorderColor.tpl 2014-10-09 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
  function hexcolorTonumbercolor(hexcolor) {
    var hexcolorAlphabets = "0123456789ABCDEF";
    var valueNumber = new Array(3);
    var j = 0;
    if(hexcolor.charAt(0) == "#")
      hexcolor = hexcolor.slice(1);
    hexcolor = hexcolor.toUpperCase();
    for(var i=0;i<6;i+=2) {
      valueNumber[j] = (hexcolorAlphabets.indexOf(hexcolor.charAt(i)) * 16) + hexcolorAlphabets.indexOf(hexcolor.charAt(i+1));
      j++;
    }
    return(valueNumber);
  }

  window.addEvent('domready', function() {

    var r = new MooRainbow('myRainbow2', {
    
      id: 'myDemo2',
      'startColor':hexcolorTonumbercolor("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemobile.theme.button.border.color', '#3da7ca') ?>"),
      'onChange': function(color) {
        $('sitemobile_theme_button_border_color').value = color.hex;
      }
    });
//    showfeatured("<?php // echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.feature.image', 1) ?>")
  });	
</script>

<?php
echo '
	<div id="sitemobile_theme_button_border_color-wrapper" class="form-wrapper">
		<div id="sitemobile_theme_button_border_color-label" class="form-label">
			<label for="sitemobile_theme_button_border_color" class="optional">
				' . $this->translate('Button Border') . '
			</label>
		</div>
		<div id="sitemobile_theme_button_border_color-element" class="form-element">
			<p class="description">' . $this->translate('Select the color for your button border. (Click on the rainbow below to choose your color.)') . '</p>
			<input name="sitemobile_theme_button_border_color" id="sitemobile_theme_button_border_color" value=' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemobile.theme.button.border.color', '#3da7ca') . ' type="text">
			<input name="myRainbow2" id="myRainbow2" src="'.$this->layout()->staticBaseUrl.'application/modules/Sitemobile/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>

<script type="text/javascript">
//  function showfeatured(option) {
//    if(option == 1) {
//      $('sitemobile_theme_button_border_color-wrapper').style.display = 'block';
//    }
//    else {
//      $('sitemobile_theme_button_border_color-wrapper').style.display = 'none';
//    }
//  }
</script>