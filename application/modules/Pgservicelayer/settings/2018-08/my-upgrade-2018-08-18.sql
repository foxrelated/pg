/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  Stars Developer
 * Created: Aug 18, 2018
 */

UPDATE `engine4_gg_tasks` SET `enabled` = '1' WHERE `plugin` = 'Sdparentalguide_Plugin_Task_Guide';

INSERT INTO `engine4_gg_tasks` (`title`, `module`, `plugin`, `per_page`, `enabled`) VALUES
('Recalculate Views for Guides',	'Sdparentalguide',	'Sdparentalguide_Plugin_Task_CalGuideViews',	50,	1),
('Recalculate Clicks for Guides',	'Sdparentalguide',	'Sdparentalguide_Plugin_Task_CalGuideClicks',	50,	1);