<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    SettingsController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class User_SettingsController extends Siteapi_Controller_Action_Standard {
    public function init() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        if (empty($viewer_id)) {
            $this->_forward('throw-error', 'settings', 'user', array(
                "error_code" => "unauthorized"
            ));
            return;
        } else {
            // Can specifiy custom id
            $id = $this->getRequestParam('id', null);
            $subject = null;
            if (null === $id) {
                $subject = Engine_Api::_()->user()->getViewer();
                Engine_Api::_()->core()->setSubject($subject);
            } else {
                $subject = Engine_Api::_()->getItem('user', $id);
                Engine_Api::_()->core()->setSubject($subject);
            }
        }
    }

    /**
     * Throw the init constructor errors.
     *
     * @return array
     */
    public function throwErrorAction() {
        $message = $this->getRequestParam("message", null);
        if (($error_code = $this->getRequestParam("error_code")) && !empty($error_code)) {
            if (!empty($message))
                $this->respondWithValidationError($error_code, $message);
            else
                $this->respondWithError($error_code);
        }

        return;
    }

    /**
     * User Settings: GET and POST General tab form.
     * 
     * @return array
     */
    public function generalAction() {
        // Config vars
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $userSettings = Engine_Api::_()->getDbtable('settings', 'user');
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $user = Engine_Api::_()->core()->getSubject();
        $facebookTwitterIntegrate = $this->getRequestParam('facebookTwitterIntegrate', 0);


        if ($this->getRequest()->isGet()) {
            $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
            $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');

            //get twitter id
            $tinfo = $twitterTable->select()
                    ->from($twitterTable)
                    ->where('user_id = ?', $viewer->getIdentity())
                    ->query()
                    ->fetch();

            $twitter_id = 0;
            if (isset($tinfo) && !empty($tinfo)) {
                $twitter_id = isset($tinfo['twitter_uid']) && !empty($tinfo['twitter_uid']) ? 1 : 0;
            }

            //get facebook id
            $info = $facebookTable->select()
                    ->from($facebookTable)
                    ->where('user_id = ?', $viewer->getIdentity())
                    ->query()
                    ->fetch();
            $facebook_uid = 0;
            if (isset($info) && !empty($info)) {
                $facebook_uid = isset($info['facebook_uid']) && !empty($info['facebook_uid']) ? 1 : 0;
            }


            $response = array();
            $response['form'] = Engine_Api::_()->getApi('Siteapi_Core', 'user')->getGeneralForm($user, $viewer, $facebookTwitterIntegrate);
            $response['formValues'] = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($user, array('email'));
            $response['formValues']['facebook_id'] = $facebook_uid;
            $response['formValues']['twitter_id'] = $twitter_id;
            $this->respondWithSuccess($response);
        } else if ($this->getRequest()->isPut() || $this->getRequest()->isPost()) {
            /* CREATE THE BLOG IN THE FOLLOWING CASES:  
             * - IF THERE ARE POST METHOD AVAILABLE.
             * - IF THERE ARE FORM POST VALUES AVAILABLE IN VALUES PARAMETER.
             */
            try {
                $values = array();
                $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'user')->getGeneralForm($user, $viewer, $facebookTwitterIntegrate);

                foreach ($getForm as $element) {
                    if (isset($_REQUEST[$element['name']]))
                        $values[$element['name']] = $_REQUEST[$element['name']];
                }

                // Form Validation
                $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'user')->getGeneralFormValidators();
                $values['validators'] = $validators;
                $validationMessage = $this->isValid($values);
                if (!empty($validationMessage) && @is_array($validationMessage)) {
                    $this->respondWithValidationError('validation_fail', $validationMessage);
                }

                if (isset($values['email']) && !empty($values['email'])) {
                    $values['email'] = @trim($values['email']);
                }

                // -- Process --
                // Check email against banned list if necessary
                if (isset($values['email']) && ($values['email'] != $user->email)) {

                    $userObj = Engine_Api::_()->getDbtable('users', 'user');
                    $isEmailExist = $userObj->select()
                            ->from($userObj, new Zend_Db_Expr('TRUE'))
                            ->where('email = ?', $values['email'])
                            ->limit(1)
                            ->query()
                            ->fetchColumn();

                    if (!empty($isEmailExist))
                        $this->respondWithError('email_not_found_or_already_registered');

                    $bannedEmailsTable = Engine_Api::_()->getDbtable('BannedEmails', 'core');
                    if ($bannedEmailsTable->isEmailBanned($values['email']))
                        $this->respondWithError('email_not_found');
                }

                // Check username against banned list if necessary.    
                if (isset($values['username']) && ($values['username'] != $user->username)) {
                    $userObj = Engine_Api::_()->getDbtable('users', 'user');
                    $isUsernameExist = $userObj->select()
                            ->from($userObj, new Zend_Db_Expr('TRUE'))
                            ->where('username = ?', $values['username'])
                            ->limit(1)
                            ->query()
                            ->fetchColumn();

                    if (!empty($isUsernameExist))
                        $this->respondWithError('username_not_found_or_already_registered');

                    $bannedUsernamesTable = Engine_Api::_()->getDbtable('BannedUsernames', 'core');
                    if ($bannedUsernamesTable->isUsernameBanned($values['username']))
                        $this->respondWithError('username_not_found');
                }

                // Set values for user object
                $user->setFromArray($values);

                // If username is changed
                $aliasValues = Engine_Api::_()->fields()->getFieldsValuesByAlias($user);
                $user->setDisplayName($aliasValues);

                $user->save();

                // Update facebook settings
                $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
                $facebook = $facebookTable->getApi();
                if (isset($facebook) && isset($values['facebook_id'])) {
                    $info = $facebookTable->select()
                            ->from($facebookTable)
                            ->where('user_id = ?', $user->getIdentity())
                            ->query()
                            ->fetch();
                    if (is_array($info) && !empty($info['facebook_uid']) &&
                            !empty($info['access_token']) && !empty($info['code'])) {
                        if (empty($values['facebook_id'])) {
                            // Remove integration
                            $facebookTable->delete(array(
                                'user_id = ?' => $user->getIdentity(),
                            ));
                            $facebook->clearAllPersistentData();
                        }
                    }
                }

                // Update twitter settings
                try {
                    unset($_SESSION['twitter_token2']);
                    unset($_SESSION['twitter_secret2']);
                    unset($_SESSION['twitter_token']);
                    unset($_SESSION['twitter_secret']);
                    $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
                    $info = $twitterTable->select()
                            ->from($twitterTable)
                            ->where('user_id = ?', $user->getIdentity())
                            ->query()
                            ->fetch();
                    if (is_array($info) &&
                            !empty($info['twitter_secret']) &&
                            !empty($info['twitter_token'])) {
                        // Update twitter settings

                        $twitter = $twitterTable->getApi();
                        if (isset($twitter) && isset($values['twitter_id'])) {

                            if (empty($values['twitter_id'])) {
                                // Remove integration
                                $twitterTable->delete(array(
                                    'user_id = ?' => $user->getIdentity(),
                                ));
                            }
                        }
                    }
                } catch (Exception $ex) {
                    $this->respondWithValidationError('internal_server_error', $ex->getMessage());
                }


//    // Update janrain settings
//    if( !empty($values['janrainnoshare']) ) {
//      $userSettings->setSetting($user, 'janrain.no-share', true);
//    } else {
//      $userSettings->setSetting($user, 'janrain.no-share', null);
//    }

                $this->successResponseNoContent('no_content');
            } catch (Exception $e) {
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

    /**
     * User Settings: GET and POST Privacy tab form.
     * 
     * @return array
     */
    public function privacyAction() {
        $user = Engine_Api::_()->core()->getSubject();
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'member', 'network', 'registered', 'everyone');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if ($this->getRequest()->isGet()) {
            $formValues = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($user);

            // Set value for view.
            foreach ($roles as $role) {
                if (1 === $auth->isAllowed($user, $role, 'view')) {
                    $formValues['privacy'] = $role;
                }
            }

            // Set value for comments.
            foreach ($roles as $role) {
                if (1 === $auth->isAllowed($user, $role, 'comment')) {
                    $formValues['comment'] = $role;
                }
            }
            // Set value for publishTypes.
            $actionTypesEnabled = Engine_Api::_()->getDbtable('actionSettings', 'activity')->getEnabledActions($user);
            foreach ($actionTypesEnabled as $key => $value) {
                $actionTypesEnabledArray["0$key"] = $value;
            }
            $form = Engine_Api::_()->getApi('Siteapi_Core', 'user')->getPrivacyForm($user);

            $getBlockedUsers = $this->getRequestParam('getBlockedUsers', 0);
            $formValues['publishTypes'] = $actionTypesEnabledArray;
            if (Engine_Api::_()->authorization()->isAllowed('user', $user, 'block') && isset($getBlockedUsers) && !empty($getBlockedUsers)) {
                $form = array();
                $form[] = array(
                    'type' => 'blockList',
                    'name' => 'blockList',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Blocked Members'),
                    'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Adding a person to your block list makes your profile (and all of your other content) unviewable to them. Any connections you have to the blocked person will be canceled. To add someone to your block list, visit that person\'s profile page.'),
                );
                $form = array_merge($form, Engine_Api::_()->getApi('Siteapi_Core', 'user')->getPrivacyForm($user));
                foreach ($user->getBlockedUsers() as $blocked_user_id) {
                    $user = Engine_Api::_()->user()->getUser($blocked_user_id);
                    if (!empty($user->getIdentity()))
                        $users[] = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($user);
                }

                if (isset($users) && !empty($users) && count($users) > 0)
                    $formValues['blockList'] = $users;
            }

            $this->respondWithSuccess(array(
                'form' => $form,
                'formValues' => $formValues
            ));
            return;
        } else if ($this->getRequest()->isPut() || $this->getRequest()->isPost()) {
            try {
                $values = array();
                if (isset($_REQUEST['search']))
                    $user->setFromArray(array("search" => $_REQUEST['search']))->save();

                if (isset($_REQUEST['privacy'])) {
                    $values['privacy'] = $_REQUEST['privacy'];
                    $viewMax = array_search($values['privacy'], $roles);
                    foreach ($roles as $i => $role) {
                        $auth->setAllowed($user, $role, 'view', ($i <= $viewMax));
                    }
                }

                if (isset($_REQUEST['comment'])) {
                    $values['comment'] = $_REQUEST['comment'];
                    $commentMax = array_search($values['comment'], $roles);
                    foreach ($roles as $i => $role) {
                        $auth->setAllowed($user, $role, 'comment', ($i <= $commentMax));
                    }
                }

                // Update notification settings
                if (isset($_REQUEST['publishTypes']) && !empty($_REQUEST['publishTypes'])) {
                    $publishTypes = Zend_Json::decode($_REQUEST['publishTypes']);
                    $publishTypes[] = 'signup';
                    $publishTypes[] = 'post';
                    $publishTypes[] = 'status';
                    Engine_Api::_()->getDbtable('actionSettings', 'activity')->setEnabledActions($user, (array) $publishTypes);
                }

                $this->successResponseNoContent('no_content');
            } catch (Exception $e) {
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

    /**
     * User Settings: GET and POST Network tab form.
     * 
     * @return array
     */
    public function networkAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $response['joinedNetworks'] = $response['availableNetworks'] = array();

        $select = Engine_Api::_()->getDbtable('membership', 'network')->getMembershipsOfSelect($viewer)
                ->order('engine4_network_networks.title ASC');
        $networks = Engine_Api::_()->getDbtable('networks', 'network')->fetchAll($select);
        foreach ($networks as $network) {
            if ($network->membership()->isMember($viewer)) {
                $response['joinedNetworks'][] = $network->toArray();
            }
        }

        // Get networks to suggest
        $network_suggestions = array();
        $table = Engine_Api::_()->getItemTable('network');
        $select = $table->select()
                ->where('assignment = ?', 0)
                ->order('title ASC');

        if (null !== ($text = $this->_getParam('text', $this->_getParam('text')))) {
            $select->where('`' . $table->info('name') . '`.`title` LIKE ?', '%' . $text . '%');
        }

        $data = array();
        foreach ($table->fetchAll($select) as $network) {
            if (!$network->membership()->isMember($viewer)) {
                $response['availableNetworks'][] = $network->toArray();
            }
        }

        if ($this->getRequest()->isGet()) {
            $this->respondWithSuccess($response);
        } else if ($this->getRequest()->isPost() && isset($_REQUEST['join_id'])) {
            // Process
            $viewer = Engine_Api::_()->user()->getViewer();

            if (empty($_REQUEST['join_id']))
                $this->respondWithValidationError('parameter_missing', 'join_id');

            $network = Engine_Api::_()->getItem('network', $_REQUEST['join_id']);
            if (null === $network) {
                $this->respondWithError('no_record');
            } else if ($network->assignment != 0) {
                $this->respondWithError('no_record');
            } else {
                $network->membership()->addMember($viewer)
                        ->setUserApproved($viewer)
                        ->setResourceApproved($viewer);

                if (!$network->hide) {
                    // Activity feed item
                    Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $network, 'network_join');
                }

                $this->successResponseNoContent('no_content');
            }
        } else if ($this->getRequest()->isPost() && isset($_REQUEST['leave_id'])) {
            if (empty($_REQUEST['leave_id']))
                $this->respondWithValidationError('parameter_missing', 'leave_id');

            $network = Engine_Api::_()->getItem('network', $_REQUEST['leave_id']);
            if (null === $network) {
                $this->respondWithError('no_record');
            } else if ($network->assignment != 0) {
                $this->respondWithError('no_record');
            } else {
                $network->membership()->removeMember($viewer);

                $this->successResponseNoContent('no_content');
            }
        }
    }

    /**
     * User Settings: GET and POST Notification tab form.
     * 
     * @return array
     */
    public function notificationsAction() {
        $user = Engine_Api::_()->core()->getSubject();

        // Build the different notification types
        $modules = Engine_Api::_()->getDbtable('modules', 'core')->getModulesAssoc();
        $notificationTypes = Engine_Api::_()->getDbtable('notificationTypes', 'activity')->getNotificationTypes();
        $notificationSettings = Engine_Api::_()->getDbtable('notificationSettings', 'activity')->getEnabledNotifications($user);

        $notificationTypesAssoc = array();
        $notificationSettingsAssoc = array();
        $getAllNotificationTypes = array();
        foreach ($notificationTypes as $type) {
            if (in_array($type->module, array('core', 'activity', 'fields', 'authorization', 'messages', 'user'))) {
                $elementName = 'general';
                $category = 'General';
            } else if (isset($modules[$type->module])) {
                $elementName = preg_replace('/[^a-zA-Z0-9]+/', '-', $type->module);
                $category = $modules[$type->module]->title;
            } else {
                $elementName = 'misc';
                $category = 'Misc';
            }

            $tempType = 'ACTIVITY_TYPE_' . strtoupper($type->type);
            $notificationTypesAssoc[$elementName]['category'] = $this->translate($category);
            $notificationTypesAssoc[$elementName]['types'][] = array(
                'type' => 'Checkbox',
                'name' => $type->type,
                'label' => $this->translate($tempType)
            );
            if (in_array($type->type, $notificationSettings))
                $notificationSettingsAssoc[] = $type->type;

            $getAllNotificationTypes[] = $type->type;
        }

        @ksort($notificationTypesAssoc);

        $notificationTypesAssoc = array_filter(array_merge(array(
            'general' => array(),
            'misc' => array(),
                        ), $notificationTypesAssoc));


        // Convert array key's to numeric.
        $tempNotificationTypesAssoc = array();
        foreach ($notificationTypesAssoc as $value)
            $tempNotificationTypesAssoc[] = $value;

        if ($this->getRequest()->isGet()) {
            if (!empty($notificationSettingsAssoc) && !empty($notificationTypesAssoc)) {
                $this->respondWithSuccess(array(
                    'form' => $tempNotificationTypesAssoc,
                    'formValues' => $notificationSettingsAssoc
                ));
            }
        } else if ($this->getRequest()->isPut() || $this->getRequest()->isPost()) {
            try {
                foreach ($_REQUEST as $key => $value) {
                    if (empty($value) || !in_array($key, $getAllNotificationTypes))
                        continue;

                    $values[] = $key;
                }

                // Set notification setting
                Engine_Api::_()->getDbtable('notificationSettings', 'activity')
                        ->setEnabledNotifications($user, $values);

                $this->successResponseNoContent('no_content');
            } catch (Exception $e) {
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

    /**
     * User Settings: GET and POST Password tab form.
     * 
     * @return array
     */
    public function passwordAction() {
        $user = Engine_Api::_()->core()->getSubject();

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if ($this->getRequest()->isGet()) {
            $this->respondWithSuccess(Engine_Api::_()->getApi('Siteapi_Core', 'user')->getChangePasswordForm($user));
        } else if ($this->getRequest()->isPost()) {
            $values = $_REQUEST;

            // Form Validation
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'user')->getChangePasswordValidators($user);
            $values['validators'] = $validators;
            $validationMessage = $this->isValid($values);
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            // Check conf
            if ($values['passwordConfirm'] !== $values['password'])
                $this->respondWithError('password_mismatch');

            // Process form
            $userTable = Engine_Api::_()->getItemTable('user');
            $db = $userTable->getAdapter();

            // Check old password
            $salt = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.secret', 'staticSalt');
            $select = $userTable->select()
                    ->from($userTable, new Zend_Db_Expr('TRUE'))
                    ->where('user_id = ?', $user->getIdentity())
                    ->where('password = ?', new Zend_Db_Expr(sprintf('MD5(CONCAT(%s, %s, salt))', $db->quote($salt), $db->quote($values['oldPassword']))))
                    ->limit(1)
            ;
            $valid = $select
                    ->query()
                    ->fetchColumn()
            ;

            if (!$valid)
                $this->respondWithError('old_password_mismatch');

            // Save
            $db->beginTransaction();

            try {
                $user->setFromArray($values);
                $user->save();

                $db->commit();
                $this->successResponseNoContent('no_content');
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

    /**
     * User Settings: Delete user account
     * 
     * @return array
     */
    public function deleteAction() {
        // Validate request methods
        $this->validateRequestMethod('DELETE');

        $user = Engine_Api::_()->core()->getSubject();
        if (!$this->_helper->requireAuth()->setAuthParams($user, null, 'delete')->isValid())
            $this->respondWithError('unauthorized');

        if (1 === count(Engine_Api::_()->user()->getSuperAdmins()) && 1 === $user->level_id)
            $this->respondWithError('unauthorized');

        // Process
        $db = Engine_Api::_()->getDbtable('users', 'user')->getAdapter();
        $db->beginTransaction();
        try {
            // Delete respective oauth token
            Engine_Api::_()->getApi('oauth', 'siteapi')->removeAccessOauthToken($user);

            $user->delete();

            $db->commit();
            $this->successResponseNoContent('no_content');
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    public function subscriptionAction() {
        $this->validateRequestMethod();

        $user = Engine_Api::_()->user()->getViewer();
        $package_id = $this->getRequestParam("package_id", null);
        Engine_Api::_()->getApi('Core', 'siteapi')->setLocal();
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        $settings = Engine_Api::_()->getApi('settings', 'core');

        // Set the translations for zend library.
        if (!Zend_Registry::isRegistered('Zend_Translate'))
            Engine_Api::_()->getApi('Core', 'siteapi')->setTranslate();

        $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
        $packagesTable = Engine_Api::_()->getDbtable('packages', 'payment');

        $getHost = Engine_Api::_()->getApi('core', 'siteapi')->getHost();
        $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $baseUrl = @trim($baseUrl, "/");

        try {
            $currentSubscription = $subscriptionsTable->fetchRow(array(
                'user_id = ?' => $user->getIdentity(),
                'active = ?' => true,
            ));

            // Get current package
            if ($currentSubscription) {
                $currentPackage = $packagesTable->fetchRow(array(
                    'package_id = ?' => $currentSubscription->package_id,
                ));
            }
            // Send Package Info after selection of package id
            if (isset($package_id) && !empty($package_id)) {
                $selectedPackage = $packagesTable->fetchRow(array(
                    'package_id = ?' => $package_id,
                ));
                $packageDescription = $selectedPackage->getPackageDescription();
                $response['selected_plan'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate('Are you sure that you want to change your subscription plan ? You will be charged : ') . $packageDescription;
                $response['description_payment'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate('If yes, click the button below and you will be taken to a payment page. When you have completed your payment, please remember to click the button that takes you back to our site.');
                $response['description_refund'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate('Please note that no refund will be provided for any unused portion of your current plan.');

                // Cancel any other existing subscriptions
                Engine_Api::_()->getDbtable('subscriptions', 'payment')
                        ->cancelAll($user, 'User cancelled the subscription.', $currentSubscription);


                // Insert the new temporary subscription
                $db = $subscriptionsTable->getAdapter();
                $db->beginTransaction();

                try {
                    $subscription = $subscriptionsTable->createRow();
                    $subscription->setFromArray(array(
                        'package_id' => $selectedPackage->package_id,
                        'user_id' => $user->getIdentity(),
                        'status' => 'initial',
                        'active' => false, // Will set to active on payment success
                        'creation_date' => new Zend_Db_Expr('NOW()'),
                    ));
                    $subscription->save();

                    // If the package is free, let's set it active now and cancel the other
                    if ($selectedPackage->isFree()) {
                        $subscription->setActive(true);
                        $subscription->onPaymentSuccess();
                        if ($currentSubscription) {
                            $currentSubscription->cancel();
                        }
                    }

                    $subscription_id = $subscription->subscription_id;
                    $db->commit();
                } catch (Exception $e) {
                    $db->rollBack();
                }

                // Check if the subscription is ok
                if ($selectedPackage->isFree() && $subscriptionsTable->check($user)) {
                    $response['webViewRedirectURL'] = "";
                } else {
                    $getOauthToken = Engine_Api::_()->getApi('oauth', 'siteapi')->getAccessOauthToken($user);
                    $response['webViewRedirectURL'] = $getHost . '/' . $baseUrl . "/payment/subscription/gateway?token=" . $getOauthToken['token'] . "&subscription_id=" . $subscription_id . "&disableHeaderAndFooter=1";
                }
                $this->respondWithSuccess($response);
            }

            if (isset($currentPackage) && !empty($currentPackage)) {
                $response['current_subsciption_plan'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($currentPackage->title);
                $response['current_subsciption_price'] = $currentPackage->getPackageDescription();
            }

            // Get available subscriptions
            $packagesTable = Engine_Api::_()->getDbtable('packages', 'payment');
            $packagesSelect = $packagesTable
                    ->select()
                    ->from($packagesTable)
                    ->where('enabled = ?', true)
                    ->where('after_signup = ?', true);

            $multiOptions = array();
            $packagesObj = $packagesTable->fetchAll($packagesSelect);
            if ((count($packagesObj) > 0)) {
                $showForm = 1;
            } elseif ((Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() > 0 &&
                    Engine_Api::_()->getDbtable('packages', 'payment')->getEnabledNonFreePackageCount() > 0)) {
                $showForm = 1;
            }
            foreach ($packagesObj as $package) {
                if ($package->package_id == $currentPackage->package_id)
                    continue;
                $userCurrency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
                $pacakageDescription = ($package->isFree()) ? "(" . Engine_Api::_()->getApi('Core', 'siteapi')->translate("Free") . ")" : "";

                $multiOptions[$package->package_id]['label'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($package->title) . $pacakageDescription;

                if (isset($package->price) && !empty($package->price) && $package->price > 0) {
                    $multiOptions[$package->package_id]['price'] = (int) $package->price;
                    $multiOptions[$package->package_id]['currency'] = (string) $userCurrency;
                }
            }

            // Element: package_id
            if (count($multiOptions) > 0 && isset($showForm) && !empty($showForm)) {
                $packageForm[] = array(
                    'type' => 'Radio',
                    'name' => 'package_id',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('If you would like to change your subscription, please select an option below.'),
                    'multiOptions' => $multiOptions,
                );

                // Init submit
                $packageForm[] = array(
                    'type' => 'Submit',
                    'name' => 'submit',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Continue'),
                );
            }
            $response['subscription_form'] = $packageForm;

            $this->respondWithSuccess($response);
        } catch (Exception $e) {
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

}
