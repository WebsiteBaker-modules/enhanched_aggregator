<?php

/*
Copyright (C) 2007, Pixel Media Pty. Ltd. 
http://www.pixelmedia.com.au
admin@pixelmedia.com.au

This module is free software. 
You can redistribute it and/or modify it 
under the terms of the GNU General Public License
 - version 2 or later, as published by the Free Software Foundation: 
http://www.gnu.org/licenses/gpl.html.

This module is distributed in the hope that it will be useful, 
but WITHOUT ANY WARRANTY; without even the implied warranty of 
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
GNU General Public License for more details.
*/

if(defined('WB_URL')) {
	
	//create mod_aggregator table:
	$database->query("DROP TABLE IF EXISTS `".TABLE_PREFIX."mod_enhanced_aggregator`");
	$mod_create_table = 'CREATE TABLE `'.TABLE_PREFIX.'mod_enhanced_aggregator` ( '
				. ' `section_id` INT NOT NULL DEFAULT \'0\' ,'
				. ' `page_id` INT NOT NULL DEFAULT \'0\','
				. ' `aggregate_hidden` BOOL NOT NULL DEFAULT \'1\','
				. ' `lines_per_page` INT NOT NULL DEFAULT \'15\','
				. ' `items_per_line` INT NOT NULL DEFAULT \'1\','
				. ' `item_class` VARCHAR(30) NOT NULL DEFAULT \'aggregator\','
				. ' `aggregator_id` VARCHAR(30) NOT NULL DEFAULT \'\','				
				. ' `page_browser_top` BOOL NOT NULL DEFAULT \'1\','
				. ' `page_browser_bottom` BOOL NOT NULL DEFAULT \'0\','
				. ' `thumb_size` INT NOT NULL DEFAULT \'0\','
				. ' `image_class` VARCHAR(30) NOT NULL DEFAULT \'\','
				. ' `use_title` BOOL NOT NULL DEFAULT \'1\','
				. ' `summary_tag` VARCHAR(15) NOT NULL DEFAULT \'\','
				. ' `summary_class` VARCHAR(30) NOT NULL DEFAULT \'\','
				. ' `remove_summary_html` BOOL NOT NULL DEFAULT \'1\','
				. ' `use_page_id_list` BOOL NOT NULL DEFAULT \'0\','
				. ' `page_id_list` TEXT NULL DEFAULT \'\','
				. ' PRIMARY KEY (section_id)'				
				. ' )';
	$database->query($mod_create_table);
	
}
?>