<?php
require_once "common.php";

if (isset($_GET['file'])) {
    $filename = rel2abs( sanitizeString($_GET['file']) );
} else {
    $filename = 'data/20100908/gfs00Z/wspd01.png';
}

// Target size
if (isset($_GET['width'])) {                 // Requested width
    $dst_w =  sanitizeString($_GET['width']);
} else {
    $dst_w = null;
}

if (isset($_GET['height'])) {               // Requested height
    $dst_h =  sanitizeString($_GET['height']);
} else {
    $dst_h = null;
}

// Central point
if (isset($_GET['top'])) {                  // Requested pointer X
    $src_y =  sanitizeString($_GET['top']);
} else {
    $src_y = 120;
}
$src_y += 260;  // cut off the title bar


if (isset($_GET['left'])) {                 // Requested point Y
    $src_x =  sanitizeString($_GET['left']);
} else {
    $src_x = 0;
}
$src_x += 100;
//echo $src_x, ", ", $src_y,'<br>';

// Get image display size
if (isset($_GET['orgwidth'])) {             // Current Display Width
    $org_w =  sanitizeString($_GET['orgwidth']);
} else {
    $org_w = 1000;
}

if (isset($_GET['orgheight'])) {           // Current display height
    $org_h =  sanitizeString($_GET['orgheight']);
} else {
    $org_h = 1000;
}

if (isset($_GET['level'])) {               // Requested Zoom level
    $zlevel =  sanitizeString($_GET['level']);
} else {
    $zlevel = 2;
}

$new_width  = $dst_w;
$new_height = $dst_h;

$fileLevel = getNetFileLevels($filename, $zlevel);
if ($fileLevel['same']) {
    // Source and destination are the same, it is an experimental feature to show the capability
    //$src_y += 260;
    //$src_x += 100;
    $dst_h = intval($dst_h/$zlevel);
    $dst_w = intval($dst_w/$zlevel);
    $src_x -= $dst_w/2;
    $src_y -= $dst_h/2;

    // Get original image size
    list($width, $height) = getimagesize($filename); 

    $image   = imagecreatefrompng($filename);                 // Source
    $image_p = imagecreatetruecolor($new_width, $new_height); // destination
    imagecopyresized($image_p,$image,0,0,$src_x,$src_y,$new_width,$new_height,$dst_w,$dst_h);
} else {
    // Get original image size
    list($width, $height) = getimagesize($fileLevel['name']); 

    $src_x = intval($src_x*$width/$org_w);
    $src_y = intval($src_y*$height/$org_h);    
    $src_x -= $dst_w/2;
    $src_y -= $dst_h/2;

    $image   = imagecreatefrompng($fileLevel['name']);                 // Source
    $image_p = imagecreatetruecolor($new_width, $new_height); // destination
    imagecopyresized($image_p,$image,0,0,$src_x,$src_y,$new_width,$new_height,$dst_w,$dst_h);
    imagecopy($image_p,$image,0,0,$src_x,$src_y,$new_width,$new_height);
}
// Content type
header('Content-type: image/png');

// Output
imagepng($image_p);
//print $fileLevel['same'];
//print $fileLevel['name'];

// clear resources
imagedestroy($image);
imagedestroy($image_p);
?>
