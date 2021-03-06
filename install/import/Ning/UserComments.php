<?php

class Install_Import_Ning_UserComments extends Install_Import_Ning_Abstract
{
  protected $_fromFile = 'ning-members-local.json';

  protected $_fromFileAlternate = 'ning-members.json';

  protected $_toTable = 'engine4_core_comments';

  protected $_resourceType = 'user';

  public function reset()
  {
    $this->_deleteComments();
  }

  protected function  _translateRow(array $data, $key = null)
  {
    if( empty($data['comments']) ) {
      return false;
    }

    $userIdentity = $this->getUserMap($data['contributorName']);

    $comments = $data['comments'];
    foreach( array_reverse($comments) as $commentData ) {
      $commentUserIdentity = $this->getUserMap($commentData['contributorName']);

      // Insert into comments?
      $this->getToDb()->insert('engine4_core_comments', array(
        'resource_type' => $this->_resourceType,
        'resource_id' => $commentUserIdentity,
        'poster_type' => $this->_resourceType,
        'poster_id' => $userIdentity,
        'body' => $this->_translateCommentBody($commentData['description']),
        'creation_date' => $this->_translateTime($commentData['createdDate']),
      ));

      //Set Activty
      $action = array(
        'type' => 'post',
        'subject_type' => $this->_resourceType,
        'subject_id' => $commentUserIdentity,
        'object_type' => $this->_resourceType,
        'object_id' => $userIdentity,
        'body' => $this->_translateCommentBody($commentData['description']),
        'date' => $this->_translateTime($commentData['createdDate']),
      );

      $action['targetTypes'] = array(
          'owner' => $userIdentity,
          'parent' => $userIdentity,
          'members' => $userIdentity,
          'registered' => 0,
          'everyone' => 0,
      );
      $key = strtotime($this->_translateTime($commentData['createdDate']));
      $this->setActivity($key, $action);
    }

    return false;
  }
}
