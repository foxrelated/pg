<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
	$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl
  	              . 'application/modules/Seaocore/externals/styles/styles.css');

include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/common_style_css.tpl';
?>
<?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.postedby', 1);?>
<script language="javascript" type="text/javascript">
  var urlhome="<?php echo $this->url(array(),"sitegroup_homesponsored",true); ?>";
</script>
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/scripts/slideitmoo-1.1_full_source.js');
?>
<a id="group_profile_members_anchor" style="position:absolute;"></a>
<script language="javascript" type="text/javascript"> 
  var total = '<?php echo $this->totalCount; ?>';

  var forward_link;
  var fwdbck_click  = 1;
 
  var limit='<?php echo $this->limit * 2; ?>';
  var curnt_limit='<?php echo $this->limit; ?>';
  var category_id='<?php echo $this->category_id; ?>';
  var titletruncation='<?php echo $this->titletruncation; ?>';
  var startindex = -1;
  var call_count = 1;
</script>
<script language="javascript" type="text/javascript">
  var slideshow;
  window.addEvents ({
    'domready': function() {
      slideshow = new SlideItMoo({
        overallContainer: 'SlideItMoo_outer',
        elementScrolled: 'SlideItMoo_inner',
        thumbsContainer: 'SlideItMoo_items',
        itemsVisible:curnt_limit,
        elemsSlide:curnt_limit,
        duration:8000,
        itemsSelector: '.SlideItMoo_element',
        itemWidth:132,
        showControls:1,
        startIndex:1,
        transition: Fx.Transitions.linear, /* transition */
        onChange: function(index) { call_count = 1;
        }
      });

      $('SlideItMoo_back').addEvent('click', function () {slideshow.sendajax(-1,slideshow);
        call_count = 1;

      });

      $('SlideItMoo_forward').addEvent('click', function () { slideshow.sendajax(1,slideshow);
        call_count = 1;
      });

       
      if((total-curnt_limit)<=0){
        // hidding forward button
        document.getElementById('SlideItMoo_forward').style.display= 'none';
        document.getElementById('SlideItMoo_back_dis').style.display= 'none';
      }
    }
  });
  var obj_sliditmoo;
  function custom_sliditmoo (obj, direction) {


    obj_sliditmoo = new SlideItMoo({
      overallContainer: 'SlideItMoo_outer',
      elementScrolled: 'SlideItMoo_inner',
      thumbsContainer: 'SlideItMoo_items',
      itemsVisible:curnt_limit,
      elemsSlide:curnt_limit,
      duration:<?php echo $this->interval; ?>,
      itemsSelector: '.SlideItMoo_element',
      itemWidth:132,
      showControls:1,
      startIndex:1,
      onChange: function(index) { call_count = 1;
      }
    });

    obj_sliditmoo.slide(direction, obj_sliditmoo);

    if(startindex<=0 && direction== -1){
      // hidding back button
      document.getElementById('SlideItMoo_back').style.display= 'none';
      document.getElementById('SlideItMoo_back_dis').style.display= 'block';
    }else{
      // vissible back button
      document.getElementById('SlideItMoo_back').style.display= 'block';
      document.getElementById('SlideItMoo_back_dis').style.display= 'none';

    }

    if(((startindex>(total-limit)|| (startindex>=(total-limit))) && direction== 1) || ((startindex>=(total-curnt_limit)) && direction==  -1)){
      // hidding forward button
      document.getElementById('SlideItMoo_forward').style.display= 'none';
      document.getElementById('SlideItMoo_forward_dis').style.display= 'block';

    }else{
      // vissible forward button
      document.getElementById('SlideItMoo_forward').style.display= 'block';
      document.getElementById('SlideItMoo_forward_dis').style.display= 'none';
    }
    fwdbck_click = 1;
  }
</script>
<ul class="seaocore_sponsored_widget">
  <li>
    <?php $sitegroup_advsitegroup = Zend_Registry::isRegistered('sitegroup_advsitegroup') ? Zend_Registry::get('sitegroup_advsitegroup') : null; ?>
    <div id="SlideItMoo_outer" class="seaocore_sponsored_carousel">
      <div class="seaocore_sponsored_carousel_back" id="SlideItMoo_back" style="display:none;">
        <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/sitegroup_carousel_up.png', '', array('align' => '', 'onMouseOver' => 'this.src="'.$this->layout()->staticBaseUrl.'application/modules/Sitegroup/externals/images/sitegroup_carousel_up_hover.png";', 'onMouseOut' => 'this.src="'.$this->layout()->staticBaseUrl.'application/modules/Sitegroup/externals/images/sitegroup_carousel_up.png";', 'border' => '0')) ?>
      </div>
       <div class="seaocore_sponsored_carousel_back" id="SlideItMoo_back_loding" style="display:none;">
        <?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/images/core/loading.gif", '', array('align'=>'', 'border'=>'0'));  ?>
      </div>
      <div class="seaocore_sponsored_carousel_back_dis" id="SlideItMoo_back_dis" style="display:block;">
        <?php if (!empty($sitegroup_advsitegroup)) {
          echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/sitegroup_carousel_up_dis.png', '', array('align' => '', 'border' => '0'));
        } else {
          return;
        } ?>
      </div>
      <div id="SlideItMoo_inner" class="seaocore_sponsored_carousel_inner">
        <div id="SlideItMoo_items" class="seaocore_sponsored_carousel_item_list">
          <?php foreach ($this->sitegroups as $sitegroup): ?>
            <div class="SlideItMoo_element seaocore_sponsored_carousel_items">
              <div class="seaocore_sponsored_carousel_items_thumb">
                  <?php echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($sitegroup->group_id, $sitegroup->owner_id, $sitegroup->getSlug()), $this->itemPhoto($sitegroup, 'thumb.icon', $sitegroup->getTitle()), array('rel' => 'lightbox[galerie]', 'class' => "thumb_icon")) ?>
              </div>
              <div class="seaocore_sponsored_carousel_items_info">
                <div class="seaocore_sponsored_carousel_items_title">
									<?php
									$item_title = Engine_Api::_()->sitegroup()->truncation($sitegroup->getTitle(), $this->titletruncation);
									echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($sitegroup->group_id, $sitegroup->owner_id, $sitegroup->getSlug()), $item_title, array('title' => $sitegroup->getTitle()))
									?>
                </div>  
                <?php if($postedBy):?>
                  <div class="seaocore_sponsored_carousel_items_stat seaocore_txt_light">
                    <?php echo $this->translate('created by'); ?>
                    <?php echo $this->htmlLink($sitegroup->getOwner()->getHref(), Engine_Api::_()->sitegroup()->truncation($sitegroup->getOwner()->getTitle(), 100), array('title' => $sitegroup->getOwner()->getTitle())) ?>
                  </div>	
                <?php endif;?>
              </div>
            </div>	                  
          <?php endforeach; ?>
        </div>
      </div>
      <div class="seaocore_sponsored_carousel_forward" id ="SlideItMoo_forward">
        <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/sitegroup_carousel_down.png', '', array('align' => '', 'onMouseOver' => 'this.src="'. $this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/sitegroup_carousel_down_hover.png";', 'onMouseOut' => 'this.src="'. $this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/sitegroup_carousel_down.png";', 'border' => '0')) ?>
      </div>

      <div class="seaocore_sponsored_carousel_forward" id="SlideItMoo_forward_loding"  style="display: none;">
        <?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/images/core/loading.gif", '', array('align'=>'', 'border'=>'0'));  ?>
      </div>
      <div class="seaocore_sponsored_carousel_forward_dis" id="SlideItMoo_forward_dis" style="display:none;">
        <?php if (!empty($sitegroup_advsitegroup)) {
          echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/sitegroup_carousel_down_dis.png', '', array('align' => '', 'border' => '0'));
        } else {
          exit();
        } ?>
      </div>
    </div>
    <div class="clr"></div>
  </li>
</ul>