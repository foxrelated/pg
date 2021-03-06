<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pgservicelayer
 * @author     Stars Developer
 */

class Pgservicelayer_GuideController extends Pgservicelayer_Controller_Action_Api
{
    public function init(){
        parent::init();        
    }
    
    public function indexAction(){
        try{
            $method = strtolower($this->getRequest()->getMethod());
            if($method == 'get'){
                $this->getAction();
            }
            else if($method == 'post'){
                $this->postAction();
            }
            else if($method == 'put' || $method == 'patch'){
                $this->putAction();
            }
            else if($method == 'delete'){
                $this->deleteAction();
            }
            else{
                $this->respondWithError('invalid_method');
            }
        } catch (Exception $ex) {
            $this->respondWithServerError($ex);
        }
    }
    
    public function getAction(){
        $viewer = Engine_Api::_()->user()->getViewer();
        if(!$viewer->getIdentity() && $this->isApiRequest()){
            $this->respondWithError('unauthorized');
        }
        
        $page = $this->getParam("page",1);
        $limit = $this->getParam("limit",50);
        $table = Engine_Api::_()->getDbTable("guides","sdparentalguide");
        $select = $table->select();
        
        
        $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($limit);
        
        $response['ResultCount'] = 0;
        $response['Results'] = array();
        $response['contentType'] = "";
        if($page > $paginator->count()){
            $this->respondWithSuccess($response);
        }
        $responseApi = Engine_Api::_()->getApi("V1_Response","pgservicelayer");
        foreach($paginator as $guide){
            ++$response['ResultCount'];
            $response['contentType'] = Engine_Api::_()->sdparentalguide()->mapSEResourceTypes($guide->getType());
            $response['Results'][] = $responseApi->getGuideData($guide);
        }
        $this->respondWithSuccess($response);
    }
    
    public function postAction(){
        $viewer = Engine_Api::_()->user()->getViewer();
        
        if(!$viewer->getIdentity()){
            $this->respondWithError('unauthorized');
        }
        $canCreate = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sdparentalguide_guide', "create");
        if (!$canCreate) {
            $this->respondWithError('unauthorized');
        }
        
        $form = Engine_Api::_()->getApi("V1_Forms","pgservicelayer")->getGuideForm();
        $validators = Engine_Api::_()->getApi("V1_Validators","pgservicelayer")->getGuideValidators();
        $values = $data = $_REQUEST;

        foreach ($form as $element) {
            if (isset($_REQUEST[$element['name']])){
                $values[$element['name']] = $_REQUEST[$element['name']];
            }
        }
        $values['validators'] = $validators;
        $validationMessage = $this->isValid($values);
        if (!empty($validationMessage) && @is_array($validationMessage)) {
            $this->respondWithValidationError('validation_fail', $validationMessage);
        }
        
        //Values for database
        $values = array(
            'title' => $this->getParam("title"),
            'topic_id' => $this->getParam("topicID"),
            'status' => $this->getParam("status",'draft'),
            'description' => $this->getParam("longDescription"),
            'photo_id' => (int)$this->getParam("coverPhotoID",0),
            'auth_view' => $this->getParam("authView","everyone"),
            'auth_comment' => $this->getParam("authComment","everyone"),
            'owner_id' => (int)$viewer->getIdentity(),
            'approved' => $this->getParam("status",0),
            'featured' => $this->getParam("status",0),
            'sponsored' => $this->getParam("status",0),
            'new' => $this->getParam("status",0),
        );
        
        $table = Engine_Api::_()->getDbTable("guides","sdparentalguide");
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            if(Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sdparentalguide_guide', "approve")){
                $values['approved'] = 1;                
            }
            $oldTz = date_default_timezone_get();
            date_default_timezone_set($viewer->timezone);
            $published_date= time();
            date_default_timezone_set($oldTz);
            $values['published_date'] = date('Y-m-d H:i:s', $published_date);
            $guide = $table->createRow();
            $guide->setFromArray($values);
            $guide->save();
            
            if($guide->status != 'draft' && $guide->approved){
                Engine_Api::_()->pgservicelayer()->updateUserCount(array('gg_guide_count' => (++$viewer->gg_guide_count)),$viewer->user_id);
            }
            
            if (empty($values['auth_view'])) {
                $values['auth_view'] = "everyone";
            }

            if (empty($values['auth_comment'])) {
                $values['auth_comment'] = "everyone";
            }
            
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            $viewMax = array_search($values['auth_view'], $roles);
            $commentMax = array_search($values['auth_comment'], $roles);

            foreach ($roles as $i => $role) {
                $auth->setAllowed($guide, $role, "view", ($i <= $viewMax));
                $auth->setAllowed($guide, $role, "comment", ($i <= $commentMax));
            }
            
            $db->commit();
            
            $responseApi = Engine_Api::_()->getApi("V1_Response","pgservicelayer");
            $response['ResultCount'] = 1;
            $response['contentType'] = Engine_Api::_()->sdparentalguide()->mapSEResourceTypes($guide->getType());
            $response['Results'] = array();
            $response['Results'][] = $responseApi->getGuideData($guide);
            $this->respondWithSuccess($response);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
        
    }
    
    public function putAction(){
        $viewer = Engine_Api::_()->user()->getViewer();
        
        if(!$viewer->getIdentity()){
            $this->respondWithError('unauthorized');
        }
        $id = $this->getParam("guideID");
        $guide = Engine_Api::_()->getItem("sdparentalguide_guide",$id);
        if(empty($guide)){
            $this->respondWithError('no_record');
        }
        $canCreate = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sdparentalguide_guide', "edit");
        if (!$canCreate) {
            $this->respondWithError('unauthorized');
        }
                
        //Values for database
        $values = array(
            'title' => $this->getParam("title",$guide->title),
            'topic_id' => $this->getParam("topicID",$guide->topic_id),
            'status' => $this->getParam("status",$guide->status),
            'description' => $this->getParam("longDescription",$guide->description),
            'photo_id' => (int)$this->getParam("coverPhotoID",$guide->photo_id),
            'approved' => $this->getParam("status",$guide->approved),
            'featured' => $this->getParam("status",$guide->featured),
            'sponsored' => $this->getParam("status",$guide->sponsored),
            'new' => $this->getParam("status",$guide->new),
            'auth_view' => $this->getParam("authView","everyone"),
            'auth_comment' => $this->getParam("authComment","everyone"),
            'owner_id' => (int)$viewer->getIdentity()
        );
        
        $table = Engine_Api::_()->getDbTable("guides","sdparentalguide");
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            if(Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sdparentalguide_guide', "approve")){
                $values['approved'] = 1;                
            }
            if(!$guide->approved && !empty($values['approved'])){
                $oldTz = date_default_timezone_get();
                date_default_timezone_set($viewer->timezone);
                $published_date= time();
                date_default_timezone_set($oldTz);
                $values['published_date'] = date('Y-m-d H:i:s', $published_date);
                $owner = $guide->getOwner();
                if($guide->status != 'draft'){
                    Engine_Api::_()->pgservicelayer()->updateUserCount(array('gg_guide_count' => (++$owner->gg_guide_count)),$owner->user_id);
                }
            }
            if(($guide->approved && empty($values['approved'])) || $guide->status == 'draft'){
                $owner = $guide->getOwner();
                Engine_Api::_()->pgservicelayer()->updateUserCount(array('gg_guide_count' => (--$owner->gg_guide_count)),$owner->user_id);
            }
            $guide->setFromArray($values);
            $guide->save();
            
            if (empty($values['auth_view'])) {
                $values['auth_view'] = "everyone";
            }

            if (empty($values['auth_comment'])) {
                $values['auth_comment'] = "everyone";
            }
            
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            $viewMax = array_search($values['auth_view'], $roles);
            $commentMax = array_search($values['auth_comment'], $roles);

            foreach ($roles as $i => $role) {
                $auth->setAllowed($guide, $role, "view", ($i <= $viewMax));
                $auth->setAllowed($guide, $role, "comment", ($i <= $commentMax));
            }
            
            $db->commit();
            
            $responseApi = Engine_Api::_()->getApi("V1_Response","pgservicelayer");
            $response['ResultCount'] = 1;
            $response['contentType'] = Engine_Api::_()->sdparentalguide()->mapSEResourceTypes($guide->getType());
            $response['Results'] = array();
            $response['Results'][] = $responseApi->getGuideData($guide);
            $this->respondWithSuccess($response);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
        
    }
    
    public function deleteAction(){
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $level_id = !empty($viewer_id) ? $viewer->level_id : Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        $id = $this->getParam("reviewID");
        $idsArray = (array)$id;
        if(is_string($id) && !empty($id)){
            $idsArray = array($id);
        }
        $guides = Engine_Api::_()->getItemMulti("sdparentalguide_guide",$idsArray);
        if (empty($guides)) {
            $this->respondWithError('no_record');
        }
        $table = Engine_Api::_()->getItemTable('sdparentalguide_guide');
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            foreach($guides as $guide){
                $canDelete = Engine_Api::_()->authorization()->getPermission($level_id, 'sdparentalguide_guide', "delete");
                if (!$canDelete) {
                    $this->respondWithError('unauthorized');
                }
                $guide->gg_deleted = 1;
                $guide->save();
                if($guide->approved && $guide->status != 'draft'){
                    $owner = $guide->getOwner();
                    Engine_Api::_()->pgservicelayer()->updateUserCount(array('gg_guide_count' => (--$owner->gg_guide_count)),$owner->user_id);
                }
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithServerError($ex);
        }
        $this->successResponseNoContent('no_content');
    }
}
