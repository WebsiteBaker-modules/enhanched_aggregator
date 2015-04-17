<?php

/*
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

// prevent this file from being accessed directly
if(defined('WB_PATH') == false) {
	exit("Cannot access this file directly"); 
}


if ($module_version < 1.10)
{
	echo "<p><b>Updating database</b></p>";
	$database->query("ALTER TABLE ".TABLE_PREFIX."mod_enhanced_aggregator ADD use_page_id_list` BOOL NOT NULL DEFAULT '0'");
	if($database->is_error()) {
		$admin->print_error($database->get_error(), ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
	}
	$database->query("ALTER TABLE ".TABLE_PREFIX."mod_enhanced_aggregator ADD page_id_list TEXT NULL");
	if($database->is_error()) {
		$admin->print_error($database->get_error(), ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
	}
}

echo "<p><b>Module '$module_name' updated to version $new_module_version</b></p>";

?>