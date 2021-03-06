<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sdparentalguide
 * @author     Stars Developer
 */

class Sdparentalguide_Model_DbTable_ListingRatings extends Engine_Db_Table
{
    protected $_rowClass = "Sdparentalguide_Model_ListingRating";
    protected $_name = 'gg_listing_ratings';
    
    public function getRating(Core_Model_Item_Abstract $listing, User_Model_User $user){
        $select = $this->select()->where('listing_id = ?',$listing->getIdentity())->where('user_id = ?',$user->getIdentity())
                ->where('listing_type = ?',$listing->getType());
        return $this->fetchRow($select);
    }
    public function getAvgListingRating(Core_Model_Item_Abstract $item){
        $select = $this->select()
                ->from($this->info("name"),array(new Zend_Db_Expr("COUNT(listingrating_id) as ratings_count"),
                    new Zend_Db_Expr("SUM(review_rating) as total_review_rating"),new Zend_Db_Expr("SUM(product_rating) as total_product_rating")))
                ->where('listing_id = ?',$item->getIdentity())
                ->where('listing_type = ?',$item->getType());
        $rating = $this->fetchRow($select);
        if(empty($rating)){
            return array(
                'author_rating' => 0,
                'product_rating' => 0
            );
        }
        return array(
            'author_rating' => $rating->ratings_count?(round(($rating->total_review_rating/$rating->ratings_count),1)):0,
            'product_rating' => $rating->total_product_rating?(round(($rating->total_product_rating/$rating->ratings_count),1)):0
        );
    }
} 




