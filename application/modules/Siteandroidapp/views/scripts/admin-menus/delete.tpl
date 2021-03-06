<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteandroidapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    delete.tpl 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<form method="post" class="global_form_popup">
    <div>
        <h3><?php echo $this->translate('Delete Menu / Category?'); ?></h3>
        <p>
            <?php echo $this->translate('Are you sure that you want to delete this menu / category? After deleting, it will not get displayed in your App\'s Dashboard.'); ?>
        </p>
        <br />
        <p>
            <input type="hidden" name="confirm" value="<?php echo $this->id ?>"/>
            <button type='submit'><?php echo $this->translate('Delete'); ?></button>
            or <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
        </p>
    </div>
</form>
<?php if (@$this->closeSmoothbox): ?>
    <script type="text/javascript">
        TB_close();
    </script>
<?php endif; ?>