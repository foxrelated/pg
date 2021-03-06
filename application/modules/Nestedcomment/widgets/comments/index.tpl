<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Nestedcomment
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-11-07 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headTranslate(
  array('Write a comment...', 'Write a reply...', 'Attach a Photo',
        'Post a comment...', 'Post a reply...')
); ?>

<?php if ($this->subjectSet): ?>

  <?php if ($this->loaded_by_ajax): ?>
    <script type="text/javascript">
      var params = {
        requestParams:<?php echo json_encode($this->params) ?>,
        responseContainer: $$('.layout_nestedcomment_comments')
      }
      en4.nestedcomment.ajaxTab.attachEvent('<?php echo $this->identity ?>', params);
    </script>
  <?php endif; ?>

  <?php
//@TODO add bundle
//    $this->headScript()
//            ->appendFile($this->layout()->staticBaseUrl . 'externals/mdetect/mdetect' . ( APPLICATION_ENV != 'development' ? '.min' : '' ) . '.js')
//            ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Nestedcomment/externals/scripts/core.js')
//            ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Nestedcomment/externals/scripts/composer.js')
//            ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
//            ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
//            ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
//            ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js')
//            ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Nestedcomment/externals/scripts/composer_tag.js')
//            ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Nestedcomment/externals/scripts/like.js')
  ?>
  <?php
  $this->headLink()->appendStylesheet(
    $this->layout()->staticBaseUrl
    . 'application/modules/Seaocore/externals/styles/style_comment.css'
  );
  $this->headLink()
    ->appendStylesheet(
      $this->layout()->staticBaseUrl
      . 'application/modules/Seaocore/externals/styles/style_infotooltip.css'
    );
  ?>

  <?php
//@TODO add bundle
//  $this->headScript()
//            ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Nestedcomment/externals/scripts/composer_photo.js')
//            ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Nestedcomment/externals/scripts/composer_link.js')
//            ->appendFile($this->layout()->staticBaseUrl . 'externals/fancyupload/Swiff.Uploader.js')
//            ->appendFile($this->layout()->staticBaseUrl . 'externals/fancyupload/Fx.ProgressBar.js')
//            ->appendFile($this->layout()->staticBaseUrl . 'externals/fancyupload/FancyUpload2.js');
    $this->headLink()
      ->appendStylesheet(
        $this->layout()->staticBaseUrl . 'externals/fancyupload/fancyupload.css'
      );
    $this->headTranslate(
      array(
        'Overall Progress ({total})', 'File Progress', 'Uploading "{name}"',
        'Upload: {bytesLoaded} with {rate}, {timeRemaining} remaining.',
        '{name}',
        'Remove', 'Click to remove this entry.', 'Upload failed',
        '{name} already added.',
        '{name} ({size}) is too small, the minimal file size is {fileSizeMin}.',
        '{name} ({size}) is too big, the maximal file size is {fileSizeMax}.',
        '{name} could not be added, amount of {fileListMax} files exceeded.',
        '{name} ({size}) is too big, overall filesize of {fileListSizeMax} exceeded.',
        'Server returned HTTP-Status <code>#{code}</code>',
        'Security error occurred ({text})',
        'Error caused a send or load operation to fail ({text})',
        'Add Photo',
        'Add Link',
        'Select File',
        'cancel',
        'Loading...',
        'Unable to upload photo. Please click cancel and try again',
        'Last',
        'Next',
        'Attach',
        'Loading...',
        'Don\'t show an image',
        'Choose Image:',
        '%d of %d',
      )
    );
    ?>
    <?php $photoLightboxComment = 0;?>
    <?php $params = Zend_Controller_Front::getInstance()->getRequest()
    ->getParams();?>
    <?php if ((isset($params['lightbox_type'])
      && $params['lightbox_type'] == 'photo')
    || isset($params['action']) && $params['action'] == 'light-box-view'
  ): ?>
    <?php $photoLightboxComment = 1; ?>
  <?php endif;?>

  <script type="text/javascript">
    var composeInstanceComment;
    var parameters = <?php echo Zend_Json_Encoder::encode($params); ?>;
    var taggingContent = '<?php echo $this->taggingContent ?>';
    var showAsNested = '<?php echo $this->showAsNested ?>';
    var showAsLike = '<?php echo $this->showAsLike ?>';
    nestedcomment_content_type = '<?php echo $this->subject->getType(); ?>';
    var showAddPhoto = '<?php echo $this->showAddPhoto; ?>';
    var showAddLink = '<?php echo $this->showAddLink; ?>';
    var showSmilies = '<?php echo $this->showSmilies; ?>';
    var showDislikeUsers = '<?php echo $this->showDislikeUsers ?>';
    var showLikeWithoutIcon = '<?php echo $this->showLikeWithoutIcon ?>';
    var showLikeWithoutIconInReplies = '<?php echo $this->showLikeWithoutIconInReplies ?>';
    var nestedCommentPressEnter = '<?php echo $this->nestedCommentPressEnter;?>';
    var commentsorder = '<?php echo $this->commentsorder ?>';
    //var photoLightboxComment = 0;
    // if(typeof parameters.lightbox_type != 'undefined' && parameters.lightbox_type == 'photo')
    var photoLightboxComment = '<?php echo $photoLightboxComment;?>';

  </script>

  <?php if ($this->showContent): ?>
    <?php echo $this->action(
      "list", "comment", "nestedcomment",
      array("type"                         => $this->subject->getType(),
            "id"                           => $this->subject->getIdentity(),
            "taggingContent"               => $this->taggingContent,
            'showAsNested'                 => $this->showAsNested,
            'showAsLike'                   => $this->showAsLike,
            'showDislikeUsers'             => $this->showDislikeUsers,
            'showLikeWithoutIcon'          => $this->showLikeWithoutIcon,
            'showLikeWithoutIconInReplies' => $this->showLikeWithoutIconInReplies,
            'nestedCommentPressEnter'      => $this->nestedCommentPressEnter,
            'showSmilies'                  => $this->showSmilies,
            'showComposerOptions'          => $this->showComposerOptions,
            'photoLightboxComment'         => $photoLightboxComment,
            'commentsorder'                => $this->commentsorder)
    ); ?>
  <?php endif;?>
    
 <?php else: ?>
  <div class="tip">
        <span>
           <?php echo $this->translate(
             'You\'re a Super Admin and this message is visible only to you. The "Comments & Replies" widget is only for Content View / Profile Pages and Static Pages. You have placed this widget on a wrong page from the ‘Layout Editor’. Please place this widget on a Content View / Profile Page or a Static Page. If the page you are currently viewing is not a widgetized page, then you can enable ‘Advanced Comments’ for it from the "Manage Modules" section available in the administration of this plugin.'
           ); ?>
        </span>
  </div><br/>
<?php endif; ?>

<?php if ($this->showSmilies): ?>
  <?php include APPLICATION_PATH
    . '/application/modules/Nestedcomment/views/scripts/_smiliesComment.tpl' ?>
<?php endif; ?>
<?php if ($this->settings('sitereaction.collection.active', 1)
  && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled(
    'sitereaction'
  )
): ?>
  <?php echo $this->stickerCollections()->render() ?>
<?php endif; ?>
