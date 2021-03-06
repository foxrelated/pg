/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  Stars Developer
 * Created: Aug 16, 2018
 */

INSERT INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('sdparentalguide_admin_permission_guides',	'sdparentalguide',	'Guides',	'',	'{\"route\":\"admin_default\",\"module\":\"sdparentalguide\",\"controller\":\"permission\",\"action\":\"guides\"}',	'sdparentalguide_admin_permission',	'',	1,	0,	3);


DROP TABLE IF EXISTS `engine4_gg_guides`;
CREATE TABLE `engine4_gg_guides` (
  `guide_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL,
  `description` longtext,
  `owner_id` int(11) DEFAULT NULL,
  `topic_id` int(11) NOT NULL DEFAULT '0',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `status` varchar(32) NOT NULL,
  `approved` tinyint(4) NOT NULL DEFAULT '0',
  `featured` tinyint(4) NOT NULL DEFAULT '0',
  `published_date` datetime NOT NULL,
  `sponsored` tinyint(4) NOT NULL DEFAULT '0',
  `new` tinyint(4) NOT NULL DEFAULT '0',
  `guide_item_count` int(11) NOT NULL DEFAULT '0',
  `photo_id` int(11) NOT NULL DEFAULT '0',
  `comment_count` int(11) NOT NULL DEFAULT '0',
  `like_count` int(11) NOT NULL DEFAULT '0',
  `view_count` int(11) NOT NULL DEFAULT '0',
  `click_count` int(11) NOT NULL,
  `average_rating` double NOT NULL DEFAULT '0',
  `dislike_count` int(11) NOT NULL,
  `gg_deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`guide_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `engine4_gg_guide_items`;
CREATE TABLE `engine4_gg_guide_items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `guide_id` int(11) NOT NULL,
  `description` longtext NOT NULL,
  `sequence` int(11) NOT NULL DEFAULT '0',
  `content_type` varchar(32) DEFAULT NULL,
  `content_id` int(11) NOT NULL DEFAULT '0',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;