<?php

/*
Based on the Aggregator module by:
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

 -------------------------------------------------------------------------------------------------
  Enhanced Aggregator Module for Website Baker v2.8.x
  Module to automatically generate a 'list' or 'catalogue' of its child pages. 
  The user can specify what information of the child pages should be displayed in the list. 
 -------------------------------------------------------------------------------------------------
 
 v1.00  (Paul van der Westhuizen; 17.08.2010)
    + initial release of the Enhanced Aggregator module
	+ based on v1.4 of the aggregator module
 v1.10  (Paul van der Westhuizen; 14.03.2011)
    + module renamed to Enhanced Aggregator for better clarity, directory changed.
	+ added ability to manually select aggregated pages
 v1.11  (Paul van der Westhuizen; 15.03.2011)
	+ removed debug code that was accidently left in
 v1.20  (Paul van der Westhuizen; 21.03.2011)
	+ improved manual page selection usability
	+ added ability to manually sort selected list of pages
 v1.21  (Paul van der Westhuizen; 22.03.2011)
	+ added summary class to <p> tag
 v1.22  (Paul van der Westhuizen; 21.04.2011)
	+ Bugfix: Wrong path in call to jquery-min.js
v1.23 (Marmot 21.05.2012)
	+ Changed pagination to group items
 -----------------------------------------------------------------------------------------
*/

$module_directory = 'enhanced_aggregator';
$module_name = 'Enhanced Aggregator';
$module_function = 'page';
$module_version = '1.23';
$module_platform = '2.8.x';
$module_author = 'Paul van der Westhuizen - Westhouse IT. Based on Aggregator v1.4 by Igor de Oliveira Couto';
$module_license = 'GNU General Public License';
$module_guid = '87A17D72-01C3-4818-862D-6E07589CECE9';
$module_home = 'http://websitebakers.com';
$module_description = '<p>The Aggregator builds a customisable, automatic <strong>summary</strong> of all child pages below it. <br />The child pages can be of any type, even other Aggregators.</p><p>Aggregators have a variety of applications: use them to build your own image galleries, blogs, lists, catalogs, and more!';


?>