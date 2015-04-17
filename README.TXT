--------------------------------------------------------------------------------------------------------
 Module Name: AGGREGATOR
--------------------------------------------------------------------------------------------------------
 This is a page(/section) module, that automatically generates a 'list' or 'catalogue' 
 of its child pages. The user specifies what information from the pages should be displayed in the list. 
 
 Options include:
   + display page title
   + display a 'summary', which is extracted from the page's contents
     (based on an html tag (with optional + class, + id) specified by the user)

 The user can also get 'Aggregator' to search for the first 'img' tag on each page, 
 and create an automatic thumbnail of the image, which appears in the listing.

 CSS classes for list items can be specified, so user can easily format the appearance and layout
 of items in their own stylesheet.
--------------------------------------------------------------------------------------------------------

Aggregator can have any page as a child, including other Aggregators, allowing the user to build a nested hierarchy, if needed.
Aggregator can be used to build fully customised picture galleries, news or blog pages, catalogues, listings, and much more.
As each Aggregator child page is a full page in its own right, the 'Search' function of WB works perfectly with it:
search results list the individual pages where the text is found - unlike searching for an article in the current
'news' add-on, which just returns the news page itself. Individual child pages also get their own listing in search engines,
and users can define overriding keywords and page description for each.

Have fun
Igor de Oliveira Couto (icouto)


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