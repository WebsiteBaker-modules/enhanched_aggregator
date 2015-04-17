<?php

/*
Copyright (C) 2007, Pixel Media Pty. Ltd. 
http://www.pixelmedia.com.au
admin@pixelmedia.com.au

This module is free software. 
You can redistribute it and/or modify it 
under the terms of the GNU General Public License
 – version 2 or later, as published by the Free Software Foundation: 
http://www.gnu.org/licenses/gpl.html.

This module is distributed in the hope that it will be useful, 
but WITHOUT ANY WARRANTY; without even the implied warranty of 
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
GNU General Public License for more details.
*/


//This functions simply gives us a way to 'indent' the html code being outputted, by providing
//a certain number of blank spaces, according to the indent level.
//INPUT: $indent_level must be the level to which the user wants to 'indent' text
//OUTPUT: returns the appropriate number of spaces to indent the line to the required
//        indent level.
if(!function_exists('spacer')){
	function spacer($indent_level = '1'){
		if(!is_numeric($indent_level) || $indent_level < 1) { return ''; }
		$indent_text = "";
		while ($indent_level > 0){
			$indent_text .= "    ";
			$indent_level--;
		}
		return $indent_text;
	}
}



//This extracts the contents of an element inside some xhtml source. The element must be defined
//by proper opening and closing (x)html tags.
//If the element has 'nested' elements inside itself, the function will take that into account,
//and the result will contain these nested elements.
//INPUT: $source is the xhtml source to be searched.
//       $open_tag is the complete opening tag for the element whose closing tag we are searching.
//       It must be well-formed, and it must be present in $source.
//       $extract_from should be the position of $open_tag in $source - useful to speed up the function. 
//       if not given, function will search for the FIRST element of $source that matches $open_tag. 
//OUTPUT: returns the position of the closing tag for the element - or false, if none is found.
if (!function_exists('element_contents')) {
	function element_contents($source = '', $open_tag = '', $extract_from = '0' ) {
		//if there is nothing to search, exit:
		if($source == '' || $open_tag == '') { return false; }
	
		//extracting element name from the opening tag:
		$regex = '/<([a-zA-Z][a-zA-Z0-9]*)\\b[^>]*>/';
		if(preg_match($regex, $open_tag, $match) == '0'){
			//$open_tag was badly formed, and we cannot extract the element name:
			return false;
		}
		$element_name = $match[1];
			
		//establishing new regex based on the opening tag given:
		$open_regex = '#' . $open_tag . '#i';
					
		//finding the opening tag, if needed:
		if($extract_from == '0'){
			$match = array(); //erasing contents of previous find operation
			if(preg_match($open_regex, $source, $match, PREG_OFFSET_CAPTURE) == '0') {
				//$source does not contain $open_tag - element cannot be found:
				return false;
			}
			//the position of the opening tag tells us where to start extracting the contents of its element:
			$extract_from = $match[0][1] + strlen($open_tag);
		} else { $extract_from = $extract_from + strlen($open_tag); }
	
		//finding the closing tag:
		$close_regex = '@</ ?' . $element_name . ' ?>@i';
		$match = array(); //erasing contents of previous find
		//attempt to find first possible closing tag - if none found, exit:
		if (preg_match($close_regex, $source, $match, PREG_OFFSET_CAPTURE, $extract_from) == '0') { 
			return false;	
		}
		//the position of the closing tag tells us where to finish extracting the contents of its element:
		$extract_to = $match[0][1];
		$close_length = strlen($match[0][0]);
		
		//detecting nested elements:
		$close_pos = $extract_to;
		$open_pos = $extract_from;
		$open_regex = '/<' . $element_name . '\\b[^>]*>/i';
		$match = array(); //erasing contents of previous find
	
		while($open_pos < $extract_to){		
			//if we cannot find a match, we should just exit:	
			if(!preg_match($open_regex, $source, $match, PREG_OFFSET_CAPTURE, $open_pos)){ break; }
			$open_pos = $match[0][1] + strlen($match[0][0]);
			if($open_pos >= $extract_to){ break; }
			
			//we found a nested opening tag within the limits, so now we must find a following closing tag:
			$match = array();//erasing contents of previous find
			if(preg_match($close_regex, $source, $match, PREG_OFFSET_CAPTURE, ($close_pos + $close_length))){
				$close_pos = $match[0][1];				
			} else { 
				//this will happen in badly-formed html
				break; 
			} 
			$match = array(); //erasing contents of previous find 
		}
		$extract_to = $close_pos;
		return substr($source, $extract_from, ($extract_to - $extract_from));
	}
}



//This function extracts the first html element from some xhtml source code,
//matching either an element tag, class, or id.
//INPUT: $source is the source code to be searched.
//       $tag, if present, will search for an html element defined by this tag name 
//       - such as 'div', 'table', 'a', etc. 
//       $class will search inside an element's 'class' attribute.
//       $id will look for an element with a corresponding id.
//       $tag and $class can be used together, to find an element of a specific
//       type that has a certain class applied to it.
//OUTPUT: returns the contents of the first element found in the source that matches  
//        the description. If the element is a table, the element will include both 
//        opening and closing tags. If element is anything else, both tags are removed,
//        and only the element's CONTENTS are returned. It the element has no closing
//        tag (such as an 'img' element), then the tag itself is returned.
//        If no element is found, returns 'false'.
if (!function_exists('get_first_element')) {
	function get_first_element($source = NULL, $tag = NULL, $class = NULL, $id = NULL) {
		//if there is nothing to search, exit straight away:
		if(is_null($source) || ($source == '')) { return false; }
		if((is_null($tag) || ($tag == '')) && (is_null($class) || ($class == '')) && (is_null($id) || ($id == ''))) { return false; }
		//search based on element id:
		if(!is_null($id) && ($id != '')){
			$regex = '/<([a-zA-Z][a-zA-Z0-9]*)\\b[^>]*id=[\'"]'. $id . '[\'"][^>]*>/i';
		//search based on class attribute only:
		} elseif((!is_null($class) && ($class != '')) && (is_null($tag) || ($tag == ''))) {
			$regex = '/<([a-zA-Z][a-zA-Z0-9]*)\\b[^>]*class=[\'"]'. $class . '[\'"][^>]*>/i';
		//search based on element tag name only:
		} elseif((!is_null($tag) && ($tag != '')) && (is_null($class) || ($class == ''))) {
			$regex = '/<' . $tag . '\\b[^>]*>/i';
		//search based on combined element tag name & class attribute:
		} elseif((!is_null($tag) && ($tag !='')) && (!is_null($class) && ($class != ''))) {
			$regex = '/<' . $tag . '\\b[^>]*class=[\'"]'. $class . '[\'"][^>]*>/i';
		}
	
		//if no match is found, return false:
		if(preg_match($regex, $source, $match, PREG_OFFSET_CAPTURE) == '0'){ return false; }

		//if there is a match, we need to get its content:
		$contents = element_contents($source, $match[0][0], $match[0][1]);
		if($contents == false) {
			//if the element has no closing tag, we return the opening tag itself
			return $match[0][0];
		} 
		if($tag == 'table'){
			$contents = $match[0][0] . $contents . '</table>'; 
		}
		return $contents;
	}
}

?>