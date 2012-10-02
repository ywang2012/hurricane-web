<?php
require_once "common.php";

if (isset($_GET['file'])) {
    $filename = rel2abs( sanitizeString($_GET['file']) );
} else {
    $filename = 'data/20100514/mosaic/comref-2010051400_mosaic_172.gif';
}

// Get original size
//list($width, $height) = getimagesize($filename);
$width = 840;
$height = 540;
$src_y = 120;

// Target size
if (isset($_GET['width'])) {
    $dst_w =  sanitizeString($_GET['width']);
} else {
    $dst_w = null;
}

if (isset($_GET['height'])) {
    $dst_h =  sanitizeString($_GET['height']);
} else {
    $dst_h = null;
}

// Find resample ratio
$r1 = null;
$r2 = null;
if ($dst_w) {
    $dst_w = ($dst_w > $width)?$width:$dst_w;
    $r1 = $dst_w/$width;
}
if ($dst_h) {
    $dst_h = ($dst_h > $height) ? $height: $dst_h;
    $r2 = $dst_h/$height;
}

if ($r1 && $r2) {
    $r = ($r1 < $r2)? $r1: $r2;
} elseif ($r1) {
    $r = $r1;
} elseif ($r2) {
    $r = $r2;
} else {
    $r = 1.0;
}

// Get new dimensions
$new_width = $width * $r;
$new_height = $height * $r;


// Resample
$image_p = imagecreatetruecolor($new_width, $new_height);
$image = imagecreatefromgif($filename);
imagecopyresampled($image_p, $image, 0, 0, 0, $src_y, $new_width, $new_height, $width, $height);
//imagecopyresized($image_p,$image,0,0,0,$src_y,$new_width,$new_height,$width,$height);

// Content type
header('Content-type: image/gif');

// Output
imagegif($image_p);
//imagedestroy($image_p)

/*
$image = new Imagick($filename);

// If 0 is provided as a width or height parameter,
// aspect ratio is maintained
$image->thumbnailImage(100, 0);

echo $image;
*/

?>
