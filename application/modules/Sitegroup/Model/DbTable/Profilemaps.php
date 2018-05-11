<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: profilemaps.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Model_DbTable_Profilemaps extends Engine_Db_Table {

  protected $_rowClass = "Sitegroup_Model_Profilemap";

	 /**
   * If category is edit then do mapping work
   *
   * @param object sitegroup
   */
	public function editCategoryMapping($sitegroup) {

		$group_id = $sitegroup->group_id;
		$select = $this->select()->from($this->info('name'), array('profile_type'))->where('category_id = ?', $sitegroup->category_id)->limit(1);
		$resultMaps = $this->fetchAll($select)->toArray();

		//CHECK THAT PREVIOUS PROFILE TYPE IS DIFFERENT OR SAME
		if (!empty($resultMaps) && !empty($resultMaps[0]['profile_type']) && $resultMaps[0]['profile_type'] != $sitegroup->profile_type) {

			//IF PROFILE TYPE IS DIFFERENT THAN FIRST DELETE ENTRIES RELEATED TO PREVIOUS TYPE FROM FIELD TABLE
			$fieldvalueTable = Engine_Api::_()->fields()->getTable('sitegroup_group', 'values');
			$fieldvalueTable->delete(array(
							'item_id = ?' => $group_id,
			));

			$fieldsearchTable = Engine_Api::_()->fields()->getTable('sitegroup_group', 'search');
			$fieldsearchTable->delete(array(
							'item_id = ?' => $group_id,
			));

			//PUT NEW PROFILE TYPE
			$fieldvalueTable->insert(array(
							'item_id' => $group_id,
							'field_id' => 1,
							'index' => 0,
							'value' => $resultMaps[0]['profile_type'],
			));

			$sitegroup->profile_type = $resultMaps[0]['profile_type'];
			$sitegroup->save();
		} elseif (empty($resultMaps)) { //IF NEW ASSIGNED CATEGORY IS NOT MAPPED WITH ANY PROFILE TYPE
			$fieldvalueTable = Engine_Api::_()->fields()->getTable('sitegroup_group', 'values');
			$fieldvalueTable->delete(array(
							'item_id = ?' => $group_id,
			));

			$fieldsearchTable = Engine_Api::_()->fields()->getTable('sitegroup_group', 'search');
			$fieldsearchTable->delete(array(
							'item_id = ?' => $group_id,
			));

			//PUT NEW PROFILE TYPE
			$fieldvalueTable->insert(array(
							'item_id' => $group_id,
							'field_id' => 1,
							'index' => 0,
							'value' => 0,
			));

			$sitegroup->profile_type = 0;
			$sitegroup->save();
		}
	}

	/**
	* Get Mapping array
	*
	*/
	public function getMapping() {

		//MAKE QUERY
		$select = $this->select()
										->from($this->info('name'), array('category_id','profile_type'))
										->where('category_id != ?', 0);
	
		//FETCH DATA
		$mapping = $this->fetchAll($select);

		//RETURN DATA
		if(!empty($mapping)) {
			return $mapping->toArray();
		}

		return null;
	}

	/**
   * Mapping work at group creation and edition
   *
   * @param object sitegroup
   */
	public function profileMapping($sitegroup) {
		$select = $this->select()->from($this->info('name'), array('profile_type'))->where('category_id = ?', $sitegroup->category_id)->limit(1);
		$result = $this->fetchAll($select)->toArray();
		if (!empty($result)) {
			if (!empty($result[0]['profile_type'])) {
				$fieldvalueTable = Engine_Api::_()->fields()->getTable('sitegroup_group', 'values');
				$fieldvalueTable->insert(array(
								'item_id' => $sitegroup->group_id,
								'field_id' => 1,
								'index' => 0,
								'value' => $result[0]['profile_type'],
				));
			}

			$sitegroup->profile_type = $result[0]['profile_type'];
			$sitegroup->save();
		}
	}

	/**
   * Get profile_type corresponding to category_id
   *
   * @param int category_id
   */
	public function getProfileType($category_id) {

		//FETCH DATA
    $profile_type = $this->select()
                    ->from($this->info('name'), array('profile_type'))
                    ->where('category_id = ?', $category_id)
										->query()
										->fetchColumn();

		//RETURN DATA
		if(!empty($profile_type)) {
			return $profile_type;
		}

		return 0;
	}

}
?>