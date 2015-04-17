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

// THIS SCRIPT RECEIVES AN IMAGE URL, AS WELL AS REQUIRED
// WIDTH AND HEIGHT MEASUREMENTS (IN PIXELS) AND GENERATES
// A THUMBNAIL OF THE IMAGE, ON-THE-FLY, WITH THOSE MEASUREMENTS.

//if image url was not passed, exit:
if(!isset($_GET['img'])) { exit; }
$original = $_GET['img'];


//if width and height were not passed, use default values:
if(!isset($_GET['width']) || !is_numeric($_GET['width'])) {
	$new_width = 55;
} else { 	$new_width = $_GET['width']; }
if(!isset($_GET['height']) || !is_numeric($_GET['height'])) {
	$new_height = 55;
} else { $new_height = $_GET['height']; }

// Setting the resize parameters:
list($old_width, $old_height) = getimagesize($original); 
$x_ratio = $new_width / $old_width;
$y_ratio = $new_height / $old_height;
if($x_ratio < $y_ratio){
	$new_height = round($old_height * $x_ratio);
} else {
	$new_width = round($old_width * $y_ratio);
}

// Letting the browser know we are sending an image (jpg):
header('Content-type: image/jpeg'); 


// Resizing the Image:
$thumbnail = imagecreatetruecolor($new_width, $new_height);
$image = imagecreatefromjpeg($original); 
imagecopyresampled($thumbnail, $image, 0, 0, 0, 0, $new_width, $new_height, $old_width, $old_height); 

// Sending the image to the browser:
imagejpeg($thumbnail, null, 80);

// Clear out the resources:
imagedestroy($image);
imagedestroy($thumbnail);

?>