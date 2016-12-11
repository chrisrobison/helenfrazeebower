<?php
$in = $_REQUEST;
if (!$in['id']) {
   $in['id'] = rand(1,270);
}

$link = mysql_connect('localhost', 'mail', 'activate') or die('Could not connect: ' . mysql_error()); mysql_select_db('hfb') or die('Could not select database');
$result = mysql_query("select * from poem where id='".mysql_real_escape_string($in['id'])."' LIMIT 1");
$poem = mysql_fetch_assoc($result);

// Set the content-type
header('Content-Type: image/png');

// Get background image
$img = "../site/images/" . $poem['image'];
$im = imagecreatefromjpeg($img);
$picsize = getimagesize($img);

// Create some colors
$white = imagecolorallocate($im, 255, 255, 255);
$white2 = imagecolorallocatealpha($im, 0, 0, 0, 90);
$grey = imagecolorallocate($im, 128, 128, 128);
$black = imagecolorallocate($im, 0, 0, 0);

// Replace path by your own font path
$font = 'palatino.ttf';
$fontTitle = 'LucidaBrightDemiBold.ttf';
$size = 26;

function makeText($txt, $im, $size, $font, $rect, $y, $yAdj) {
   global $white, $white2, $black, $picsize;
   $box = imagettfbbox($size, 0, $font, $txt);
   $txtwidth = $box[2] - $box[0];
   $txtheight = $box[1] - $box[5];
   
   $posX = ($picsize[0] / 2) - ($txtwidth / 2);
   if (!$y) {
      $posY = ($picsize[1] / 2) - ($txtheight / 2) + $yAdj;
   } else {
      $posY = $y;
   }

   if ($rect) {
      imagefilledrectangle($im, $posX-50, 200, $posX + $txtwidth+50, 250 + $txtheight, $white2);
   }
   imagettftext($im, $size, 0, $posX+2, $posY+2, $black, $font, $txt);
   imagettftext($im, $size, 0, $posX, $posY, $white, $font, $txt);
}

$poemBox = imagettfbbox(26, 0, $font, $poem['poem']);
$titleBox = imagettfbbox(50, 0, $font, $poem['title']);
$txtwidth = $poemBox[2] - $poemBox[0];
$txtheight = $poemBox[1] - $poemBox[5];
$titlewidth = $titleBox[2] - $titleBox[0];
$titleheight = $titleBox[1] - $titleBox[5];

$h = $txtheight + 50 + 280;
$my = ($picsize[1] / 2) - ($h / 2);
$mx = ($picsize[0] / 2) - (max($txtwidth+80, $titlewidth+80)/2);

imagefilledrectangle($im, $mx, 0, $mx + max($txtwidth+80, $titlewidth), $picsize[1], $white2);
makeText($poem['title'], $im, 50, $fontTitle, 0, 100, 0);
makeText("By Helen Frazee-Bower", $im, 30, $font, 0, 170, 0);
makeText(html_entity_decode($poem['poem']), $im, 26, $font, 0, 0, 100);

imagepng($im);
imagedestroy($im);
?>
