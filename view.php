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

// Include the required aggregator functions
require_once(WB_PATH .'/modules/enhanced_aggregator/include.php');

// Get current aggregator settings:
$settings_query = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_enhanced_aggregator WHERE section_id = '$section_id'");
$agg_settings = $settings_query->fetchRow(); 

// Get list of child pages to current aggregator - including hidden pages, as per user settings:
$extra_where_sql = $wb->extra_where_sql;
if($agg_settings['aggregate_hidden']){
	$regex = '/ AND visibility != \'hidden\'/';
	$replacement = '';
	$extra_where_sql = preg_replace($regex, $replacement, $extra_where_sql); 
}
//var_dump($agg_settings);
/***** Code altered by Paul vdW *****/
// If the module is set to aggregate only selected pages 
// then we need a different query
if ($agg_settings['use_page_id_list']) {
	$pages_query = $database->query("SELECT * FROM ".TABLE_PREFIX."pages WHERE page_id IN ({$agg_settings['page_id_list']}) AND $extra_where_sql ORDER BY position ASC");
}
else {
	$pages_query = $database->query("SELECT * FROM ".TABLE_PREFIX."pages WHERE parent = '$page_id' AND $extra_where_sql ORDER BY position ASC");
}
//echo "<br /><br />SELECT * FROM ".TABLE_PREFIX."pages WHERE page_id IN ({$agg_settings['page_id_list']}) AND $extra_where_sql ORDER BY position ASC";



/***** End altered code ******/

if($pages_query->numRows() < 1) { exit(0); } //if this page has no children, we do not need to go any further
while($page = $pages_query->fetchRow()) { $items[] = $page; }

/***** Code added by Paul vdW *****/

// Now we need to sort items by the page_id_list, if we're using it
if ($agg_settings['use_page_id_list']) {
	$page_id_arr = explode(',',$agg_settings['page_id_list']);
	$sorted_arr = array(); //declare in case there aren't any
	foreach ($page_id_arr as $key=>$pid) {
		foreach ($items as $item) {
			if ($item['page_id']==$pid)
				$sorted_arr[$key]=$item;
		}
	}
	$items = $sorted_arr;
}


// If any subpages are aggregator pages then don't add their content, add content of all their children.
// Currently only the first level of children are gathered.

foreach($items as $count => $item) {
//echo '$count ='."$count<br>";
  $agg_query = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_enhanced_aggregator WHERE page_id = '" . $item['page_id'] ."'");
  if($agg_query->numRows() == 1) {	// This page is an aggregator page.
    $agg_pages[] = $count;  // Save list of agg page array keys to delete later from $items.
    // Now get all the child pages
    $children_query = $database->query("SELECT * FROM ".TABLE_PREFIX."pages WHERE parent = '" . $item['page_id'] ."' AND $extra_where_sql ORDER BY position ASC");
    if($children_query->numRows() > 0) {
//echo '$a='."$a<br>"
//echo $item['page_id'].'<br>';
//      while($child = $children_query->fetchRow()) { $new_items[] = $child; }
      while($child = $children_query->fetchRow()) { $items[] = $child; }
    }
  }
}
// Remove aggregator pages.
foreach($agg_pages as $agg) { unset($items[$agg]); }
//$items = array_merge($items, $new_items);
//print_r($items);

/***** End added code ******/



//PAGINATION CODE - useful for when the list of items is long, and the
//Aggregator needs to display more than one page. Out of the full items list, 
//this code will pick only the ones we will need to display on the current page,
// - based on which page is currently being viewed by the user:

$per_page = $agg_settings['items_per_line'] * $agg_settings['lines_per_page'];
$pagination = array();
$all_items = count($items);
$i = 1;
while ($i <= $all_items){
	$pagination[] = $i.($i == $all_items || $per_page == 1 ? "" : "-".min($i + $per_page - 1,$all_items));
	$i += $per_page;
}

$num_of_pgs = ceil(count($items) / $per_page);
if(!isset($_GET['aggpage']) || !is_numeric($_GET['aggpage']) || ($_GET['aggpage'] < 1)) { 
	$pg = 1;
	$first_item = 0; 
} else {
	$pg = ceil($_GET['aggpage']);
	//in case someone has typed an url directly, and is trying to reach page number
	//which is too high, and does not exist:
	if($pg > $num_of_pgs) { $pg = $num_of_pgs; }
	$first_item = ($pg - 1) * $per_page; 
}
if($pg != $num_of_pgs) { 
	$items = array_slice($items, $first_item, $per_page); 
} else { 
	$items = array_slice($items, $first_item); 
}


//BUILDING THE PAGE BROWSER:
$pages_query = $database->query("SELECT * FROM ".TABLE_PREFIX."pages WHERE page_id = '$page_id'");
$this_page = $pages_query->fetchRow();

$browser = $AGGTEXT['NAVIGATION'].": ";

//if we have less than 6 pages in total:
if($num_of_pgs <= 6){
	for($count = 1; $count <= $num_of_pgs; $count++){
		$nav_page = spacer(1) . "<span>" . $pagination[$count-1] . "</span>\n";
		if($count != $pg){
			$open_a_tag = spacer(1) . "<a href=\"" . WB_URL . PAGES_DIRECTORY . $this_page['link'] . PAGE_EXTENSION . "?aggpage=" . $count . "\">\n";
			$nav_page = spacer(1) . $nav_page . "\n";
			$close_a_tag = spacer(1) . "</a>\n";
		} else {
			$open_a_tag = "";
			$close_a_tag = "";
		}
		$browser .= $open_a_tag . $nav_page . $close_a_tag;
	}
} else {
	//we have more than 6 pages, and we are navigating to one of the first 3 pages:
	if(($pg - 2) < 1){
		for($count = 1; $count <= 4; $count++){
			$nav_page = spacer(1) . "<span>" . $pagination[$count-1] . "</span>\n";
			if($count != $pg){
				$open_a_tag = spacer(1) . "<a href=\"" . WB_URL . PAGES_DIRECTORY . $this_page['link'] . PAGE_EXTENSION . "?aggpage=" . $count . "\">\n";
				$nav_page = spacer(1) . $nav_page . "\n";
				$close_a_tag = spacer(1) . "</a>\n";
			} else {
				$open_a_tag = "";
				$close_a_tag = "";
			}
			$browser .= $open_a_tag . $nav_page . $close_a_tag;
		}
		$browser .= spacer(1) . "<span>...</span>\n";
		$browser .= spacer(1) . "<a href=\"" . WB_URL . PAGES_DIRECTORY . $this_page['link'] . PAGE_EXTENSION . "?aggpage=" . $num_of_pgs . "\">\n";
		$browser .= spacer(2) . "<span>" . $pagination[count($pagination)-1] . "</span>\n" . spacer(1) . "</a>\n";
		
	// we have	more than 6 pages, and we are navigating to one of the last 3 pages:
	} elseif (($pg + 2) > $num_of_pgs){
		$browser .= spacer(1) . "<a href=\"" . WB_URL . PAGES_DIRECTORY . $this_page['link'] . PAGE_EXTENSION . "?aggpage=1\">\n";
		$browser .= spacer(2) . "<span>".$pagination[0]."</span>\n" . spacer(1) . "</a>\n";
		$browser .= spacer(1) . "<span>...</span>\n";
		for($count = ($num_of_pgs - 3); $count <= $num_of_pgs; $count++){
			if($count != $pg){
				$open_a_tag = "<a href=\"" . WB_URL . PAGES_DIRECTORY . $this_page['link'] . PAGE_EXTENSION . "?aggpage=" . $count . "\">\n";
				$nav_page = spacer(1) . $pagination[$count-1] . "\n";
				$close_a_tag = spacer(1) . "</a>\n";
			} else {
				$open_a_tag = "";
				$close_a_tag = "";
				$nav_page = spacer(1) . $pagination[$count-1] . "\n";
			}
			$browser .= $open_a_tag . $nav_page . $close_a_tag;
		}
	
	//we have more than 6 pages, and we are navigating to a page somewhere in the middle:
	} else {
		$browser .= spacer(1) . "<a href=\"" . WB_URL . PAGES_DIRECTORY . $this_page['link'] . PAGE_EXTENSION . "?aggpage=1\">\n";
		$browser .= spacer(2) . "<span>".$pagination[0]."</span>\n" . spacer(1) . "</a>\n";
		if(($pg - 2) != 1){ $browser .= spacer(1) . "<span>...</span>\n"; }
		$browser .= spacer(1) . "<a href=\"" . WB_URL . PAGES_DIRECTORY . $this_page['link'] . PAGE_EXTENSION . "?aggpage=" . ($pg - 1) . "\">\n";
		$browser .= spacer(2) . "<span>" . $pagination[$pg - 2] . "</span>\n" . spacer(1) . "</a>\n";
		$browser .= spacer(1) . "<span>" . $pagination[$pg - 1] . "</span>\n";
		$browser .= spacer(1) . "<a href=\"" . WB_URL . PAGES_DIRECTORY . $this_page['link'] . PAGE_EXTENSION . "?aggpage=" . ($pg + 1) . "\">\n";
		$browser .= spacer(2) . "<span>" . $pagination[$pg] . "</span>\n" . spacer(1) . "</a>\n";
		if(($pg + 2) != $num_of_pgs){ $browser .= spacer(1) . "<span>...</span>\n"; }
		$browser .= spacer(1) . "<a href=\"" . WB_URL . PAGES_DIRECTORY . $this_page['link'] . PAGE_EXTENSION . "?aggpage=" . $num_of_pgs . "\">\n";
		$browser .= spacer(2) . "<span>" . $pagination[count($pagination)-1] . "</span>\n" . spacer(1) . "</a>\n";
	}
}


//DISPLAY TOP PAGE BROWSER
//displaying the browswer at the TOP of the aggregator, if needed:
if($agg_settings['page_browser_top']) { echo spacer(1) . "<div class=\"agg_pages\">\n" . $browser . "</div>\n"; }


//BUILDING THE AGGREGATOR:	
//if aggregator_id is present, then we declare it in the enclosing div:
$aggregator_id = ($agg_settings['aggregator_id'] != '') ? (' id="' . $agg_settings['aggregator_id'] . '"') : '';
echo spacer(1) . "<div" . $aggregator_id . ">\n";
echo spacer(2) . "<div>\n";

foreach($items as $count => $item) {
	//if this is the beginning of a new line, we need a <tr> tag
	if($count < $agg_settings['items_per_line']) {
		$tr_open_flag = false;
	} else {
		$tr_open_flag = (($count % $agg_settings['items_per_line']) == 0) ? true : false;
	}
	if($tr_open_flag) { echo spacer(2) . "<div>\n"; }
		
	//encasing the item's contents in a hyperlink pointing to the child page:
	echo spacer(4) . "<a href=\"" . WB_URL . PAGES_DIRECTORY . $item['link'] . PAGE_EXTENSION . "\"" . (($agg_settings['item_class'] != '') ? (' class="' . $agg_settings['item_class'] . '"') : '') . " >\n";
		
	//if user is allowed to see the page's content, then we retrieve it:
	$page_source = '';
	if(($agg_settings['aggregate_hidden'] && ($item['visibility'] == 'hidden')) || $wb->show_page($item)){		
		$query = "SELECT * FROM ".TABLE_PREFIX."sections WHERE page_id = '" . $item['page_id'] . "' ORDER BY block, position ASC";
		$sections_query = $database->query($query);
		if($sections_query->numRows() >= 1) {
			while($section = $sections_query->fetchRow()) {				
				$section_id = $section['section_id'];
				$module = $section['module'];
				if($section['module'] != 'enhanced_aggregator'){
					ob_start();
					include(WB_PATH.'/modules/'.$module.'/view.php');
					$page_source .= ob_get_contents();
				    ob_end_clean();
				}
			}			
		}
	}
	

	
	
	//IF USER WANTS PAGE TITLES:
	if($agg_settings['use_title']){	echo spacer(5) . "<h3>" . $item['menu_title'] . "</h3>\n"; }
	
	
	//IF USER WANTS PAGE SUMMARIES:
	//determine what type of search we'll do for a summary element
	// - we can search for an element based on id, class, tag name,
	// class AND tag name, or perform no search at all - no summary needed.
	if(($agg_settings['aggregate_hidden'] && ($item['visibility'] != 'hidden')) && !$wb->show_page($item) &&
	(($agg_settings['summary_class'] != '') || ($agg_settings['summary_tag'] != ''))){
		echo spacer(5) . "<p>" . $MESSAGE['FRONTEND']['SORRY_NO_VIEWING_PERMISSIONS'] . "</p>\n"; 
	}
	
	
	if(($agg_settings['summary_class'] != '') && ($agg_settings['summary_tag'] != '')){
		//combined tag + class search:
		$summary_tag = get_first_element($page_source, $agg_settings['summary_tag'], $agg_settings['summary_class']);
	} elseif(($agg_settings['summary_class'] == '') && ($agg_settings['summary_tag'] != '')){
		//tag only search:
		$summary_tag = get_first_element($page_source, $agg_settings['summary_tag']);
	} elseif(($agg_settings['summary_class'] != '') && ($agg_settings['summary_tag'] == '')){
		//class only search:
		$summary_tag = get_first_element($page_source, '', $agg_settings['summary_class']);
	} else {
		//if user class and tag fields blank, no summary is needed:
		$summary_tag = false; 
	}

	echo spacer(4) . "</a>\n"; //closing the title link
	if($summary_tag){ 
		if($agg_settings['remove_summary_html']) { $summary_tag = strip_tags($summary_tag); }
		echo spacer(5) . "<p" . (($agg_settings['item_class'] != '') ? (' class="' . $agg_settings['item_class'] . '"') : '') . ">\n" . spacer(6) . $summary_tag;
		echo "&nbsp;<a href=\"" . WB_URL . PAGES_DIRECTORY . $item['link'] . PAGE_EXTENSION . "\"" . (($agg_settings['item_class'] != '') ? (' class="' . $agg_settings['item_class'] . '"') : '') . " >";
		echo "Read more...</a>\n";
		echo "\n" . spacer(5) ."</p>\n"; 
	}
	
	//IF USER WANTS THUMBNAILS:
	if($agg_settings['thumb_size'] && ($page_source != '')){
		echo "<a href=\"" . WB_URL . PAGES_DIRECTORY . $item['link'] . PAGE_EXTENSION . "\"" . (($agg_settings['item_class'] != '') ? (' class="' . $agg_settings['item_class'] . '"') : '') . " >";
		//if the image must be of a specific class:
		if($agg_settings['image_class'] != ''){
			$img_tag = get_first_element($page_source,'img', $agg_settings['image_class']);
		} else {
			$img_tag = get_first_element($page_source,'img');
		}
		if(!$img_tag){ //if no image was found in the page
			$img_tag = '';
		} else { 
			//an image was found, so now we must employ the thumbnailer.php script
			//to generate a thumbnail for us - we do this by changing the original
			//src attribute in the img tag, to reference the thumbnailer script: 
			$regex = '/ height=[\'"][^\'"]*[\'"]/';
			$replacement = '';
			$img_tag = preg_replace($regex, $replacement, $img_tag); //removing existing height
			$regex = '/ width=[\'"][^\'"]*[\'"]/';
			$replacement = '';
			$img_tag = preg_replace($regex, $replacement, $img_tag); //removing existing width
			//we must find out whether the img src is a relative or absolute url:
			$regex = '/src=[\'"]([^\'"]*)[\'"]/';
			if(preg_match($regex, $img_tag, $match)) {
				$replacement = 'src="' . WB_URL . '/modules/enhanced_aggregator/thumbnailer.php?img=';
				if(preg_match('/[^:]{2,8}:/', $match[1])){
					//the src url is an absolute path:
					$replacement .= ('$1&width=' . $agg_settings['thumb_size'] . '&height=' . $agg_settings['thumb_size'] . '"');
				} else {
					//the src url is a relative path:
					$replacement .= (WB_URL . '$1&width=' . $agg_settings['thumb_size'] . '&height=' . $agg_settings['thumb_size'] . '"');
				}
				//replacing existing src attribute with thumbnailer src
				$img_tag = preg_replace($regex, $replacement, $img_tag); 
			} else {
				//img tag does not have a properly formatted 'src'
				//so we cannot create thumbnail:
				$img_tag = '';
			}
		}
		echo spacer(5) . $img_tag . "\n";
		echo "</a>\n";
	}


	//if this item is at the end of a line, we need a </div> tag
	if(($count + 1) < $agg_settings['items_per_line']){
		$tr_close_flag = false;
	} else {
		$tr_close_flag = ((($count + 1) % $agg_settings['items_per_line']) == 0) ? true : false;
	}
	if($tr_close_flag) { echo spacer(2) . "</div>\n"; }
}
//if the last item in the Aggregator was not at the end of a line, we haven't closed it!:
if(!$tr_close_flag) { echo spacer(2) . "</div>\n"; }

echo spacer(1) . "</div>\n"; //closing aggregator 


//DISPLAY BOTTOM PAGE BROWSER
//displaying the browswer at the BOTTOM of the aggregator, if needed:
if($agg_settings['page_browser_bottom']) { echo spacer(1) . "<div>\n" . $browser . "</div>\n";; }

?>