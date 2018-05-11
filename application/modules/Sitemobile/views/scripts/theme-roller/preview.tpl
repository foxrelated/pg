<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: preview.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headLink()
        ->appendStylesheet(rtrim($this->baseUrl(), '/') . '/application/modules/Sitemobile/externals/theme-roller/css/tr.preview.css')
        ->appendStylesheet(rtrim($this->baseUrl(), '/') . '/application/themes/sitemobile_tablet/' . $this->activeTheme->name . '/structure.css')
;
$this->headScript()
        ->prependFile(rtrim($this->baseUrl(), '/') . '/application/modules/Sitemobile/externals/scripts/jquery.mobile-1.3.1' . (APPLICATION_ENV == 'development' ? '' : '.min' ) . '.js')
        ->prependFile(rtrim($this->baseUrl(), '/') . '/application/modules/Sitemobile/externals/scripts/jquery' . (APPLICATION_ENV == 'development' ? '' : '.min') . '.js')
?>
<style type="text/css" id="styleblock">
</style>
<style>
  .ui-icon,
  .ui-icon-searchfield:after {

    background-image: url(application/themes/sitemobile_tablet/<?php echo  $this->activeTheme->name; ?>/images/icons-18-white.png) /*{global-icon-set}*/;
  }
  .ui-icon-alt .ui-icon,
  .ui-icon-alt .ui-icon-searchfield:after {
    background-image: url(application/themes/sitemobile_tablet/<?php echo  $this->activeTheme->name; ?>/images/icons-18-black.png);
  }

  /* loading icon */
  .ui-icon-loading {
    background: url(application/themes/sitemobile_tablet/<?php echo  $this->activeTheme->name; ?>/images/ajax-loader.gif);
  }
</style>

<div class="preview ui-shadow swatch">
  <div class="ui-header ui-bar-a" data-swatch="a" data-theme="a" data-form="ui-bar-a" data-role="header" role="banner">
    <a class="ui-btn-left ui-btn ui-btn-icon-notext ui-btn-corner-all ui-shadow ui-btn-up-a" data-iconpos="notext" data-theme="a" data-role="button" data-icon="home" title=" Home ">
      <span class="ui-btn-inner ui-btn-corner-all">
        <span class="ui-btn-text"> Home </span>
        <span data-form="ui-icon" class="ui-icon ui-icon-home ui-icon-shadow"></span>
      </span>
    </a>
    <h1 class="ui-title" tabindex="0" role="heading" aria-level="1">A</h1>
    <a class="ui-btn-right ui-btn ui-btn-icon-notext ui-btn-corner-all ui-shadow ui-btn-up-a" data-iconpos="notext" data-theme="a" data-role="button" data-icon="grid" title=" Navigation ">
      <span class="ui-btn-inner ui-btn-corner-all">
        <span class="ui-btn-text"> Navigation </span>
        <span data-form="ui-icon" class="ui-icon ui-icon-grid ui-icon-shadow"></span>
      </span>
    </a>
  </div>

  <div class="ui-content ui-body-a" data-theme="a" data-form="ui-body-a" data-role="content" role="main">

    <p>
      Sample text and <a class="ui-link" data-form="ui-body-a" href="#" data-theme="a">links</a>.
    </p>

    <ul data-role="listview" data-inset="true">
      <li data-role="list-divider" data-swatch="a" data-theme="a" data-form="ui-bar-a">List Header</li>
      <li data-form="ui-btn-up-a" data-swatch="a" data-theme="a">Read-only list item</li>
      <li data-form="ui-btn-up-a"><a href="#">Linked list item</a></li>
    </ul>

    <div data-role="fieldcontain">
      <fieldset data-role="controlgroup">
        <input type="radio" name="radio-choice-a" id="radio-choice-1-a" value="choice-1" checked="checked" />
        <label for="radio-choice-1-a" data-form="ui-btn-up-a">Radio</label>

        <input type="checkbox" name="checkbox-a" id="checkbox-a" checked="checked" />
        <label for="checkbox-a" data-form="ui-btn-up-a">Checkbox</label>

      </fieldset>
    </div>

    <div data-role="fieldcontain"> 
      <fieldset data-role="controlgroup" data-type="horizontal">
        <input type="radio" name="radio-view-a" id="radio-view-a-a" value="list" checked="checked"/> 
        <label for="radio-view-a-a" data-form="ui-btn-up-a">On</label> 
        <input type="radio" name="radio-view-a" id="radio-view-b-a" value="grid"  /> 
        <label for="radio-view-b-a" data-form="ui-btn-up-a">Off</label> 
      </fieldset> 
    </div>

    <div data-role="fieldcontain">
      <select name="select-choice-1" id="select-choice-1" data-native-menu="false" data-theme="a" data-form="ui-btn-up-a">
        <option value="standard">Option 1</option>
        <option value="rush">Option 2</option>
        <option value="express">Option 3</option>
        <option value="overnight">Option 4</option>
      </select>
    </div>

    <input type="text" value="Text Input" class="input" data-form="ui-body-a" />

    <div data-role="fieldcontain">
      <input type="range" name="slider" value="50" min="0" max="100" data-form="ui-body-a" data-theme="a" data-highlight="true" />
    </div>

    <button data-icon="star" data-theme="a" data-form="ui-btn-up-a">Button</button>
  </div>
</div><!--end_swatches-->

<div class="preview ui-shadow add-swatch" id="add-swatch">
  <h5><a href="">Add swatch...</a></h5>
</div>


<div id="highlight"> </div>

