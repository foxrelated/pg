<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: resend.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<h2>
  <?php echo $this->translate("Verification Email") ?>
</h2>

<?php if ($this->error): ?>
  <p>
    <?php echo $this->translate($this->error) ?>
  </p>

  <br />

  <h3>
    <?php echo $this->htmlLink(array('route' => 'default'), $this->translate('Back')) ?>
  </h3>
<?php else: ?>
  <p>
    <?php
    echo $this->translate('A verification message has been resent to ' .
            'your email address with instructions for activating your account. Once ' .
            'you have activated your account, you will be able to sign in.');
    ?>
  </p>

  <br />

  <h3>
  <?php echo $this->htmlLink(array('route' => 'default'), $this->translate('OK, thanks!')) ?>
  </h3>
<?php endif; ?>