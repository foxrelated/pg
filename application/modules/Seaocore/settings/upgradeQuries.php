<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: upgradeQuries.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_modules')
        ->where('name = ?', 'seaocore')
        ->where('version <= ?', '4.8.7p18');
$is_enabled = $select->query()->fetchObject();

if ($is_enabled) {

    $tablesArray = array('engine4_advancedslideshows', 'engine4_advancedslideshow_images', 'engine4_dbbackup_dbbackups', 'engine4_dbbackup_settings', 'engine4_dbbackup_backuplogs', 'engine4_communityad_target', 'engine4_communityad_adtype', 'engine4_communityad_userads', 'engine4_communityad_adcampaigns', 'engine4_communityad_adstatistics', 'engine4_communityad_adcancels', 'engine4_communityad_adtargets', 'engine4_communityad_likes', 'engine4_communityad_transactions', 'engine4_communityad_modules', 'engine4_communityad_faqs', 'engine4_communityad_infopages', 'engine4_communityad_package', 'engine4_communityad_adstatisticscache', 'engine4_communityad_adtype', 'engine4_documents', 'engine4_document_categories', 'engine4_document_fields_maps', 'engine4_document_fields_search', 'engine4_eventdocument_documents', 'engine4_eventdocument_document_fields_maps', 'engine4_eventdocument_document_fields_search', 'engine4_facebooksefeed_signupfeeds', 'engine4_facebookse_widgetsettings', 'engine4_facebookse_feedsettings', 'engine4_facebookse_mixsettings', 'engine4_facebookse_statistics', 'engine4_facebookse_comments', 'engine4_feedback_fields_maps', 'engine4_groupdocument_documents', 'engine4_groupdocument_document_fields_maps', 'engine4_groupdocument_document_fields_search', 'engine4_list_itemofthedays', 'engine4_list_vieweds', 'engine4_list_listing_fields_maps', 'engine4_list_clasfvideos', 'engine4_list_ratings', 'engine4_list_reviews', 'engine4_list_listing_fields_search', 'engine4_list_writes', 'engine4_mapprofiletypelevel_mapprofiletypelevels', 'engine4_mcard_info', 'engine4_poke_pokeusers', 'engine4_poke_settings', 'engine4_recipe_vieweds', 'engine4_recipe_itemofthedays', 'engine4_recipe_locations', 'engine4_recipe_fields_maps', 'engine4_recipe_clasfvideos', 'engine4_recipe_ratings', 'engine4_recipe_reviews', 'engine4_recipe_fields_search', 'engine4_recipe_writes', 'engine4_seaocore_tabs', 'engine4_seaocores', 'engine4_seaocore_userinfo', 'engine4_seaocore_locations', 'engine4_siteestore_users', 'engine4_siteestore_products', 'engine4_siteestore_orders', 'engine4_siteestore_languagemaps', 'engine4_sitefaq_faqs', 'engine4_sitefaq_categories', 'engine4_sitefaq_questions', 'engine4_sitefaq_faq_fields_maps', 'engine4_sitefaq_faq_fields_search', 'engine4_sitemember_ratings', 'engine4_siteslideshows', 'engine4_siteslideshow_images', 'engine4_sitestaticpage_page_fields_maps', 'engine4_sitestaticpage_page_fields_search', 'engine4_sitestaticpage_page_fields_values', 'engine4_sitetagcheckin_addlocations', 'engine4_sitetutorial_tutorials', 'engine4_sitetutorial_categories', 'engine4_sitetutorial_questions', 'engine4_sitetutorial_tutorial_fields_maps', 'engine4_sitetutorial_tutorial_fields_search', 'engine4_siteverify_verifies', 'engine4_suggestion_photos', 'engine4_sitestoredocument_documents', 'engine4_sitestoreform_fields_maps', 'engine4_sitestore_writes', 'engine4_sitestoreproduct_vieweds', 'engine4_sitestoreproduct_startuppages', 'engine4_sitestoreproduct_clasfvideos', 'engine4_sitestoreproduct_ratings', 'engine4_sitestoreproduct_product_fields_maps', 'engine4_sitestoreproduct_product_fields_search', 'engine4_sitestoreproduct_review_fields_maps', 'engine4_sitestoreproduct_review_fields_search', 'engine4_sitestoreproduct_fields_maps', 'engine4_sitestoreproduct_cartproduct_fields_maps', 'engine4_sitestoreproduct_cartproduct_fields_search', 'engine4_sitestoreproduct_downloadable_files', 'engine4_sitestoreproduct_notify_emails', 'engine4_sitestoreproduct_store_gateways', 'engine4_sitestoreproduct_storebills', 'engine4_sitestoreproduct_remaining_bills', 'engine4_sitestoreproduct_sections', 'engine4_sitestoreproduct_importfiles', 'engine4_sitestoreproduct_imports', 'engine4_sitestoreproduct_printingtags', 'engine4_sitestoreproduct_documents', 'engine4_sitestore_packages_planmaps', 'engine4_sitestoreproduct_order_downpayments', 'engine4_sitestore_otherinfo', 'engine4_sitestoreoffer_ordercoupons', 'engine4_sitereview_vieweds', 'engine4_sitereview_clasfvideos', 'engine4_sitereview_ratings', 'engine4_sitereview_locations', 'engine4_sitereview_listing_fields_maps', 'engine4_sitereview_listing_fields_search', 'engine4_sitereview_review_fields_maps', 'engine4_sitereview_review_fields_search', 'engine4_sitereview_otherinfo', 'engine4_siteevent_event_fields_maps', 'engine4_siteevent_userreviews', 'engine4_sitemobileapp_gcmusers', 'engine4_sitemobileapp_welcomeslides', 'engine4_sitemobileapp_apnusers', 'engine4_sitepagedocument_documents', 'engine4_sitepageform_fields_maps', 'engine4_sitepage_hideprofilewidgets', 'engine4_sitepage_writes', 'engine4_sitebusinessdocument_documents', 'engine4_sitebusinessform_fields_maps', 'engine4_sitebusiness_hideprofilewidgets', 'engine4_sitebusiness_writes', 'engine4_sitegroupdocument_documents', 'engine4_sitegroupform_fields_maps', 'engine4_sitegroup_hideprofilewidgets', 'engine4_sitegroup_writes', 'engine4_list_locations', 'engine4_feedbacks', 'engine4_feedback_albums', 'engine4_feedback_blockips', 'engine4_feedback_blockusers', 'engine4_feedback_categories', 'engine4_feedback_images', 'engine4_feedback_severities', 'engine4_feedback_status', 'engine4_feedback_votes', 'engine4_suggestion_albums', 'engine4_suggestion_introductions', 'engine4_suggestion_notifications', 'engine4_suggestion_rejected', 'engine4_suggestion_suggestions', 'engine4_userconnection_setting', 'engine4_sitestoreproduct_addresses', 'engine4_sitestoreproduct_cartproducts', 'engine4_sitestoreproduct_carts', 'engine4_sitestoreproduct_ordercheques', 'engine4_sitestoreproduct_orders', 'engine4_sitestoreproduct_order_addresses', 'engine4_sitestoreproduct_order_comments', 'engine4_sitestoreproduct_order_downloads', 'engine4_sitestoreproduct_order_products', 'engine4_sitestoreproduct_otherinfo', 'engine4_sitestoreproduct_payment_requests', 'engine4_sitestoreproduct_regions', 'engine4_sitestoreproduct_remaining_amounts', 'engine4_sitestoreproduct_shipping_methods', 'engine4_sitestoreproduct_shipping_trackings', 'engine4_sitestoreproduct_taxes', 'engine4_sitestoreproduct_tax_rates', 'engine4_sitestore_hideprofilewidgets', 'engine4_dbbackup_destinations', 'engine4_dbbackup_backupauthentications', 'engine4_facebookse_feedtemplates', 'engine4_facebookse_settings', 'engine4_list_listing_fields_values', 'engine4_feedback_fields_search');

    foreach ($tablesArray as $tableName) {
        $table_exist = $db->query("SHOW TABLES LIKE '$tableName'")->fetch();
        if (!empty($table_exist)) {
            $db->query("ALTER TABLE `$tableName` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;");
        }
    }
}
