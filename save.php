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


require('../../config.php');

// Include WB admin wrapper script:
$update_when_modified = true; // Tells script to update when this page was last updated
require(WB_PATH.'/modules/admin.php');


//DATA CHECK & VALIDATION:

$use_page_id_list = (($_POST['use_page_id_list'] == '1')) ? 1 : 0;
$page_id_list = ($_POST['page_ids']);


//Check flag for aggregating hidden pages:
$aggregate_hidden = (($_POST['aggregate_hidden'] == '1')) ? 1 : 0;


//Check that values for thumbnail usage are needed, and acceptable:
$use_thumb = (($_POST['use_thumbnail'] == '1')) ? 1 : 0;
$thumb_size = ($use_thumb && is_numeric($_POST['thumb_size']) && $_POST['thumb_size'] >= 1) ? round($_POST['thumb_size']) : 0;


//Check value for image extraction class, if needed:
if($thumb_size == 0){
	$img_class = '';
} else {
	$img_class = trim($_POST['image_class']);
	if(($img_class != '') && preg_match('/[^a-zA-Z0-9_-]/', $img_class)) { 
		$img_class = ''; 
		}
}


//Check flag for page title display:
$use_title = (($_POST['use_title'] == '1')) ? 1 : 0;


//Check for summary extraction information, if needed:
$use_summary = (($_POST['use_summary'] == '1')) ? 1 : 0;
if(!$use_summary){
	$summary_tag = '';
	$summary_class = '';
} else {
	$summary_tag = trim($_POST['summary_tag']);
	if($summary_tag == 'none' || ($summary_tag != '' && preg_match('/[^a-zA-Z0-9_-]/', $summary_tag))) { $summary_tag = ''; }
	$summary_class = trim($_POST['summary_class']);
	if(($summary_class != '') && preg_match('/[^a-zA-Z0-9_-]/', $summary_class)) { $summary_class = ''; }
}


//Check flag for removing summary html markup:
$remove_summary_html = (($_POST['remove_summary_html'] == '1')) ? 1 : 0;


//Check that lines_per_page & items_per_line are acceptable values:
$page_lines = (is_numeric($_POST['lines_per_page']) && ($_POST['lines_per_page'] >= 1)) ? round($_POST['lines_per_page']) : 15;
$line_items = (is_numeric($_POST['items_per_line']) && ($_POST['items_per_line'] >= 1)) ? round($_POST['items_per_line']) : 1;
if($line_items > 100) { $line_items = 1; } // >100 item/line would cause items to be 0% wide!


//Check that item_class is needed, and if so, that it has an acceptable value:
$item_class = trim($_POST['item_class']);
if(($item_class != '') && preg_match('/[^a-zA-Z0-9_-]/', $item_class)) { $item_class = ''; }


//Check that aggregator_id is needed, and if so, that it has an acceptable value:
$aggregator_id = trim($_POST['aggregator_id']);
if(($aggregator_id != '') && preg_match('/[^a-zA-Z0-9_-]/', $aggregator_id)) { $aggregator_id = ''; }


//Check flags for page browser display:
$pb_top = (($_POST['page_browser_top'] == '1')) ? 1 : 0;
$pb_btm = (($_POST['page_browser_bottom'] == '1')) ? 1 : 0;


// UPDATING AGGREGATOR SETTINGS:

$update_query = "UPDATE " . TABLE_PREFIX . "mod_enhanced_aggregator SET";
$update_query .= " aggregate_hidden = '" . $aggregate_hidden . "'";
$update_query .= ", lines_per_page = '" . $page_lines . "'";
$update_query .= ", items_per_line = '" . $line_items. "'";
$update_query .= ", item_class = '" . $item_class . "'";
$update_query .= ", aggregator_id = '" . $aggregator_id . "'";
$update_query .= ", page_browser_top = '" . $pb_top . "'";
$update_query .= ", page_browser_bottom = '" . $pb_btm . "'";
$update_query .= ", thumb_size = '" . $thumb_size . "'";
$update_query .= ", image_class = '" . $img_class . "'";
$update_query .= ", use_title = '" . $use_title . "'";
$update_query .= ", summary_tag = '" . $summary_tag . "'";
$update_query .= ", summary_class = '" . $summary_class . "'";
$update_query .= ", remove_summary_html = '" . $remove_summary_html . "'";
$update_query .= ", use_page_id_list = '" . $use_page_id_list . "'";
$update_query .= ", page_id_list = '" . $page_id_list . "'";
$update_query .= " WHERE section_id = '" . $section_id . "'";
$database->query($update_query);

// Check if there is a db error, otherwise let user know we were successful:
if($database->is_error()) {
	$admin->print_error($database->get_error(), ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
} else {
	$admin->print_success($TEXT['SUCCESS'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
}

?>