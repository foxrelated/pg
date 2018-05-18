<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sdparentalguide
 * @author     Stars Developer
 */

class Sdparentalguide_Model_DbTable_Search extends Engine_Db_Table
{
    protected $_rowClass = "Sdparentalguide_Model_Search";
    protected $_name = 'gg_search_activity';
    public function logSearch($searchText){
        if(empty($searchText)){
            return;
        }
        
        $row = $this->createRow();
        $row->search_text = $searchText;
        $row->save();
    }
} 



