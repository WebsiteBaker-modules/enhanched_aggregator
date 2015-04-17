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

// Must include, in order to stop this file being accessed directly:
if(defined('WB_PATH') == false) { exit("Cannot access this file directly"); }

// Delete row from the table
$database->query("DELETE FROM ".TABLE_PREFIX."mod_enhanced_aggregator WHERE section_id = '$section_id'");

?>