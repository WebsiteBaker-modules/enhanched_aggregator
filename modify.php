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

// Load Language file
if(LANGUAGE_LOADED) {
    require_once(WB_PATH.'/modules/enhanced_aggregator/languages/EN.php');
    if(file_exists(WB_PATH.'/modules/enhanced_aggregator/languages/'.LANGUAGE.'.php')) {
        require_once(WB_PATH.'/modules/enhanced_aggregator/languages/'.LANGUAGE.'.php');
    }
}

// Get current settings:
$query_content = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_enhanced_aggregator WHERE section_id = '$section_id'");
$settings = $query_content->fetchRow(); 

// prep selected page ID array
if(is_array(explode(',',$settings['page_id_list'])))		// only show list if there are any
	$page_id_arr = explode(',',$settings['page_id_list']);
else
	$page_id_arr = array();



// Function to generate page list
// $type: all, selected, available
function gen_page_list($parent, $selected_pages = array(), $type = 'all') {
	global $database, $admin;
	if ($type=='selected') {
		foreach($selected_pages as $page_id) { //process each individually to get output in order
			$get_pages = $database->query("SELECT * FROM ".TABLE_PREFIX."pages WHERE page_id = '{$page_id}'");	
			while($page = $get_pages->fetchRow()) {
				// method page_is_visible was introduced with WB 2.7
				if(method_exists($admin, 'page_is_visible') && !$admin->page_is_visible($page))
					continue;
				$title = stripslashes($page['menu_title']);
				echo "<option value=\"{$page['page_id']}\">$title</option>\n";
			}
		}
	}
	else {
		$get_pages = $database->query("SELECT * FROM ".TABLE_PREFIX."pages WHERE parent = '$parent'");
		while($page = $get_pages->fetchRow()) {
			$selected = '';
			// method page_is_visible was introduced with WB 2.7
			if(method_exists($admin, 'page_is_visible') && !$admin->page_is_visible($page))
				continue;
			$title = stripslashes($page['menu_title']);
			// Add leading -'s so we can tell what level a page is at
			$leading_dashes = '';
			for($i = 0; $i < $page['level']; $i++) {
				$leading_dashes .= '- ';
			}
			$title = $leading_dashes.' '.$title;
			if ($type=='all' && in_array($page['page_id'],$selected_pages)) {
				$selected = ' SELECTED';
				echo "<option value=\"{$page['page_id']}\"$selected>$title</option>\n";
			}
			elseif ($type=='available' && !in_array($page['page_id'],$selected_pages)) {
				echo "<option value=\"{$page['page_id']}\">$title</option>\n";
			}
			gen_page_list($page['page_id'],$selected_pages,$type);
		}
	}
}
?>

<style type="text/css">

input[type="submit"].aggregator, input[type="button"].aggregator {
	width: 200px;
}

ul.aggregator { 
	list-style-type: none;
	padding-left: 0;
}

li.aggregator {
	background-color: #eee;
	padding: 5px;
	margin-bottom: 10px;
}

p.aggregator, hr.aggregator {
	margin-left: 20px;
}


</style>

<h2><?php echo $AGGTEXT['AGGREGATOR_SETTINGS']; ?></h2>

<form name="edit<?php echo '_'.$section_id; ?>" action="<?php echo WB_URL; ?>/modules/enhanced_aggregator/save.php" id="agg_form" method="post">
	<input type="hidden" name="page_id" value="<?php echo $page_id; ?>" />
	<input type="hidden" name="section_id" value="<?php echo $section_id; ?>" />
	<ul class="aggregator">
		<li class="aggregator" style="overflow:auto">
			<h3><strong><?php echo $AGGTEXT['AGGREGATOR_GENERAL']; ?></strong></h3>
			<p class="aggregator">
				<input type="checkbox" name="use_page_id_list" value="1" <?php if($settings['use_page_id_list']) { echo 'checked '; } ?>/> <strong><?php echo $AGGTEXT['USE_PAGE_ID_LIST']; ?></strong>
			</p>
			<p class="aggregator" style="float:left;">
				<strong><?php echo $AGGTEXT['AVAILABLE_PAGE_LIST']; ?>:</strong>
				<br /><br />
				<select name="selectfrom" id="select-from" multiple size="10">
					<?php gen_page_list(0,$page_id_arr, 'available'); ?>
				</select>
			</p>
			<p class="aggregator" style="float:left;">
			<p class="aggregator" style="float:left;margin:100px 8px;"><a href="JavaScript:void(0);" id="btn-remove">&laquo; Remove</a></p>
			<p class="aggregator" style="float:left;margin:100px 8px;"><a href="JavaScript:void(0);" id="btn-add">Add &raquo;</a></p>
			<p class="aggregator" style="float:left;">
				<strong><?php echo $AGGTEXT['SELECTED_PAGE_LIST']; ?> :</strong><br /><br />
				<select name="select-to" id="select-to" multiple size="10">
					<?php gen_page_list(0,$page_id_arr,'selected'); ?>
				</select>
				<br /><br />
				<a href="JavaScript:void(0);" id="btn-up" style="margin:0 20px;">Up</a>
				<a href="JavaScript:void(0);" id="btn-down" style="margin:0 20px;">Down</a>
				<br /><br />
				<input type="hidden" name="page_ids" id="page_ids" value="">
			</p>
		</li>
		<li class="aggregator" style="clear:both">
			<h3><strong><?php echo $AGGTEXT['AGGREGATOR_CONTENT']; ?></strong></h3>
			<p class="aggregator">
				<input type="checkbox" name="aggregate_hidden" value="1" <?php if($settings['aggregate_hidden']) { echo 'checked '; } ?>/> <strong><?php echo $AGGTEXT['AGGREGATE_HIDDEN']; ?></strong>
			</p>
			<hr class="aggregator" />
			<p class="aggregator"><input type="checkbox" name="use_thumbnail" value="1" <?php if($settings['thumb_size']) { echo 'checked '; } ?>/> <strong><?php echo $AGGTEXT['USE_THUMBNAIL']; ?></strong></p>
			<p class="aggregator">
				<?php echo $AGGTEXT['THUMB_SIZE']; ?>: <input type="text" name="thumb_size" size="3" value="<?php echo $settings['thumb_size']; ?>" />
			</p>
			<p class="aggregator">
				<?php echo $AGGTEXT['IMAGE_CLASS']; ?>: <input type="text" name="image_class" size="30" value="<?php  echo $settings['image_class']; ?>" />
			</p>
			<hr  class="aggregator" />
			<p class="aggregator">
				<input type="checkbox" name="use_title" value="1" <?php if($settings['use_title']) { echo 'checked '; } ?>/> <strong><?php echo $AGGTEXT['USE_TITLE']; ?></strong>
			</p>
			<hr  class="aggregator" />
			<p class="aggregator"><input type="checkbox" name="use_summary" value="1" <?php
			if($settings['summary_id'] != '' || $settings['summary_tag'] != '' || $settings['summary_class'] != '') { echo 'checked'; }
			?>/><strong><?php echo $AGGTEXT['USE_SUMMARY']; ?></strong></p>
			<p class="aggregator">
				<?php echo $AGGTEXT['SUMMARY_BY_TAG']?>:
				<select name="summary_tag">
					<option value="none" <?php if($settings['summary_tag'] == '') { echo 'selected'; } ?>>-</option>
					<option value="address" <?php if($settings['summary_tag'] == 'address') { echo 'selected'; } ?>>address</option>
					<option value="pre" <?php if($settings['summary_tag'] == 'pre') { echo 'selected'; } ?>>pre</option>
					<option value="table" <?php if($settings['summary_tag'] == 'table') { echo 'selected'; } ?>>table</option>
					<option value="h1" <?php if($settings['summary_tag'] == 'h1') { echo 'selected'; } ?>>h1</option>
					<option value="h2" <?php if($settings['summary_tag'] == 'h2') { echo 'selected'; } ?>>h2</option>
					<option value="h3" <?php if($settings['summary_tag'] == 'h3') { echo 'selected'; } ?>>h3</option>
					<option value="h4" <?php if($settings['summary_tag'] == 'h4') { echo 'selected'; } ?>>h4</option>
					<option value="h5" <?php if($settings['summary_tag'] == 'h5') { echo 'selected'; } ?>>h5</option>
					<option value="h6" <?php if($settings['summary_tag'] == 'h6') { echo 'selected'; } ?>>h6</option>
					<option value="div" <?php if($settings['summary_tag'] == 'div') { echo 'selected'; } ?>>div</option>
					<option value="p" <?php if($settings['summary_tag'] == 'p') { echo 'selected'; } ?>>p</option>
				</select>
			</p>
			<p class="aggregator">
				<?php echo $AGGTEXT['SUMMARY_BY_CLASS']; ?>: <input type="text" name="summary_class" size="30" value="<?php  echo $settings['summary_class']; ?>" />
			</p>
			<p class="aggregator">
				<input type="checkbox" name="remove_summary_html" value="1" <?php if($settings['remove_summary_html']) { echo 'checked '; } ?>/> <?php echo $AGGTEXT['REMOVE_SUMMARY_HTML']; ?>
			</p>
		</li>
		<li class="aggregator">
			<h3><strong><?php echo $AGGTEXT['AGGREGATOR_LAYOUT']; ?></strong></h3>
			<p class="aggregator">
				<?php echo $AGGTEXT['LINES_PER_PAGE']; ?>: <input type="text" name="lines_per_page" size="3" value="<?php echo $settings['lines_per_page']; ?>" /> 
				<?php echo $AGGTEXT['ITEMS_PER_LINE']; ?>: <input type="text" name="items_per_line" size="3" value="<?php echo $settings['items_per_line']; ?>" />
			</p>
			<p class="aggregator">
				<?php echo $AGGTEXT['ITEM_CLASS']; ?>: <input type="text" name="item_class" size="30" value="<?php  echo $settings['item_class']; ?>" />
			</p>
			<p class="aggregator">
				<?php echo $AGGTEXT['AGGREGATOR_ID']; ?>: <input type="text" name="aggregator_id" size="30" value="<?php  echo $settings['aggregator_id']; ?>" />
			</p>
			<p class="aggregator">
				<input type="checkbox" name="page_browser_top" value="1" <?php if($settings['page_browser_top']) { echo 'checked '; } ?>/><?php echo $AGGTEXT['PAGE_BROWSER_TOP']; ?> 
				<input type="checkbox" name="page_browser_bottom" value="1" <?php if($settings['page_browser_bottom']) { echo 'checked '; } ?>/><?php echo $AGGTEXT['PAGE_BROWSER_BOTTOM']; ?>
			</p>
		</li>
	</ul>
	<table border="0" width="100%">
		<tr>
			<td align="left"><input name="save" type="button" value="<?php echo $TEXT['SAVE']; ?>" class="aggregator" onClick="setPageIDs();"></td>
			<td align="center"><a href="<?php echo WB_URL; ?>/modules/enhanced_aggregator/help/"><?php echo $MENU['HELP']; ?></a></td>
			<td align="right"><input type="button" value="<?php echo $TEXT['CANCEL']; ?>" onclick="javascript: window.location = '<?php echo ADMIN_URL; ?>/pages/modify.php?page_id=<?php echo $page_id; ?>';" class="aggregator" /></td>
		</tr>
	</table>
</form>
<script src="/include/jquery/jquery-min.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {
 
    $('#btn-add').click(function(){
        $('#select-from option:selected').each( function() {
                $('#select-to').append("<option value='"+$(this).val()+"'>"+$(this).text()+"</option>");
            $(this).remove();
        });
    });
    $('#btn-remove').click(function(){
        $('#select-to option:selected').each( function() {
            $('#select-from').append("<option value='"+$(this).val()+"'>"+$(this).text()+"</option>");
            $(this).remove();
        });
    });
     $('#btn-up').bind('click', function() {
        $('#select-to option:selected').each( function() {
            var newPos = $('#select-to option').index(this) - 1;
            if (newPos > -1) {
                $('#select-to option').eq(newPos).before("<option value='"+$(this).val()+"' selected='selected'>"+$(this).text()+"</option>");
                $(this).remove();
            }
        });
    });
    $('#btn-down').bind('click', function() {
        var countOptions = $('#select-to option').size();
        $('#select-to option:selected').each( function() {
            var newPos = $('#select-to option').index(this) + 1;
            if (newPos < countOptions) {
                $('#select-to option').eq(newPos).after("<option value='"+$(this).val()+"' selected='selected'>"+$(this).text()+"</option>");
                $(this).remove();
            }
        });
    });
});

function setPageIDs () {
	
	var values = new Array();
    $('#select-to option').each( function() {
		values.push($(this).val());
	});

	var allValues = values.join(",");
	//alert(allValues);
	$('#page_ids').val(allValues);
	
	$('#agg_form').submit();
}
</script>
