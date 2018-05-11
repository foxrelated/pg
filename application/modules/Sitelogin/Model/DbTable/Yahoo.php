<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelogin
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Yahoo.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelogin_Model_Dbtable_Yahoo extends Engine_Db_Table {

    protected $_url;

    public function getApi() {
        if (isset($_SESSION['access_token']) && !empty($_SESSION['access_token'])) {
            return;
        }
        if (isset($_GET['code']) && !empty($_GET['code'])) {
            $tokenRecieved = $this->getAccessToken();
            return $tokenRecieved;
        } else {
            $this->getAuthorizationCode();
        }
    }

    /*
     * Function to fetch access code from linkedin
     * 
     */

    function getAuthorizationCode() {
        $loginEnable = $this->yahooIntegrationEnabled();
        if (empty($loginEnable)) {
            return;
        }

        //Check if linkedin login is enable
        $yahooSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_yahoo;
        $client_id = isset($yahooSettings['clientId']) ? $yahooSettings['clientId'] : 0;
        $client_secret = isset($yahooSettings['clientSecret']) ? $yahooSettings['clientSecret'] : 0;

        $redirect_uri = $this->getYahooRedirectUrl();
        try {
            $translate = Zend_Registry::get('Zend_Translate');
            $language=$translate->getLocale();
            $params = array('response_type' => 'code',
                'client_id' => $client_id,
                'language'=> $language,
                'state' => uniqid('', true), // unique long string
                'redirect_uri' => $redirect_uri,
            );

            // Authentication request
            $this->_url = 'https://api.login.yahoo.com/oauth2/request_auth?' . http_build_query($params);
            // Needed to identify request when it returns to us
            $_SESSION['state'] = $params['state'];
            // Redirect user to authenticate
            header("Location: $this->_url");
            exit;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /*
     * Function to return access token from linkedin
     * 
     * @return boolean
     */

    function getAccessToken() {
        $loginEnable = $this->yahooIntegrationEnabled();
        if (empty($loginEnable)) {
            return;
        }

        //Check if Yahoo login is enable
        $yahooSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_yahoo;
        $client_id = isset($yahooSettings['clientId']) ? $yahooSettings['clientId'] : 0;
        $client_secret = isset($yahooSettings['clientSecret']) ? $yahooSettings['clientSecret'] : 0;

        $redirect_uri = $this->getYahooRedirectUrl();

        $params = array(
            'grant_type' => 'authorization_code',
            'client_id' => $client_id,
            'client_secret' => $client_secret,            
            'code' => $_GET['code'],
            'redirect_uri' => $redirect_uri,
        );
        try {
            $client = new Zend_Http_Client();
            $client->setUri('https://api.login.yahoo.com/oauth2/get_token')
            ->setMethod(Zend_Http_Client::POST)
            ->setParameterPost($params);

            // Process response
            $response = $client->request();
            $responseData = $response->getBody();
            $responseData = Zend_Json::decode($responseData, Zend_Json::TYPE_ARRAY);
            $_SESSION['access_token'] = $responseData['access_token'];
            $_SESSION['refresh_token'] = $responseData['refresh_token'];
        } catch (Exception $ex) {
            throw $ex;
        }
        return true;
    }

    /*
     * Function to fetch user info from Yahoo
     * 
     * @return array
     */

    function fetch() {

        $params = array('access_token' => $_SESSION['access_token'],
                        'format' => 'json');

        try {
            // Need to use HTTPS
            $this->_url = 'https://social.yahooapis.com/v1/user/me/profile?' . http_build_query($params);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->_url);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
            ob_start();
            curl_exec($ch);
            curl_close($ch);
            $response = ob_get_contents();
            ob_end_clean();

            $data=json_decode($response);
            if(isset($data->error) && isset($_SESSION['refresh_token']) && !empty($_SESSION['refresh_token'])){
                $yahooSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_yahoo;
                $client_id = isset($yahooSettings['clientId']) ? $yahooSettings['clientId'] : 0;
                $client_secret = isset($yahooSettings['clientSecret']) ? $yahooSettings['clientSecret'] : 0;
                $redirect_uri = $this->getYahooRedirectUrl();
                $params = array(
                'grant_type' => 'refresh_token',
                'client_id' => $client_id,
                'client_secret' => $client_secret,            
                'refresh_token' => $_SESSION['refresh_token'],
                'redirect_uri' => $redirect_uri,
                );
            
                $client = new Zend_Http_Client();
                $client->setUri('https://api.login.yahoo.com/oauth2/get_token')
                ->setMethod(Zend_Http_Client::POST)
                ->setParameterPost($params);

                // Process response
                $response = $client->request();
                $responseData = $response->getBody();
                $responseData = Zend_Json::decode($responseData, Zend_Json::TYPE_ARRAY);
                $_SESSION['access_token'] = $responseData['access_token'];
                $_SESSION['refresh_token'] = $responseData['refresh_token'];

                 $this->_url = 'https://social.yahooapis.com/v1/user/me/profile?' . http_build_query($params);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $this->_url);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
                ob_start();
                curl_exec($ch);
                curl_close($ch);
                $response = ob_get_contents();
                ob_end_clean();
                }
        } catch (Exception $ex) {
            throw $ex;
        }
        return json_decode($response);

    }

    /*
     * Function to return if linkedin integration is enabled or not
     * 
     * @return boolean
     */

    public function yahooIntegrationEnabled() {
        $yahooSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_yahoo;

        $client_id = isset($yahooSettings['clientId']) ? $yahooSettings['clientId'] : 0;
        $client_secret = isset($yahooSettings['clientSecret']) ? $yahooSettings['clientSecret'] : 0;
        $loginEnable = isset($yahooSettings['yahooOptions']) ? $yahooSettings['yahooOptions'] : 0;

        if (empty($client_id) || empty($client_secret) || empty($loginEnable))
            return false;

        return true;
    }
    
    public function yahooIntegration() {
        $yahooSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_yahoo;

        $client_id = isset($yahooSettings['clientId']) ? $yahooSettings['clientId'] : 0;
        $client_secret = isset($yahooSettings['clientSecret']) ? $yahooSettings['clientSecret'] : 0;
        
        if (empty($client_id) || empty($client_secret))
            return false;

        return true;
    }

    public function yahooButtonRender($action) {
        if (empty($action)) {
            return false;
        }

        $yahooSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_yahoo;
        $loginEnable = isset($yahooSettings['yahooOptions']) ? $yahooSettings['yahooOptions'] : 0;

        return (in_array($action, $loginEnable)) ? true : false;
    }
    
    public function getYahooRedirectUrl() {
        $baseParentUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $domainUrl = (_ENGINE_SSL ? 'https://' : 'http://')
                . $_SERVER['HTTP_HOST'];
        if (isset($baseParentUrl) && !empty($baseParentUrl)) {
            $domainUrl = $domainUrl . $baseParentUrl;
        }

        return $domainUrl . '/sitelogin/auth/yahoo';
    }
    public function getCount() {
      $select = $this->select()
      ->from($this->info('name'), array('COUNT(user_id) as count'));
      $results = $this->fetchRow($select);
      return $results->count;
    }

}
