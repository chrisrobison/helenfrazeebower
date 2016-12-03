<?php
   $util = new utility();
   if (!function_exists('mime_content_type')) {
      function mime_content_type($f) {
         $f = escapeshellarg($f);
         return trim( `file -bi $f` );
      }
   }
   $in = $_REQUEST;
   
   if (!$in['path']) {
   //   $in['path'] = preg_replace("/img.php/", '', $_SERVER['SCRIPT_NAME']);
   //   $in['fullpath'] = preg_replace("/img.php/", '', $_SERVER['PATH_TRANSLATED']);
      $in['path'] = "./";
   }
   
   $in['relpath'] = preg_replace("|//|", '/', $in['path']);
   $in['path'] = preg_replace("|//|", '/', preg_replace("/img.php/", '', $_SERVER['PATH_TRANSLATED'])).preg_replace("/\/?\~\w+\//", '', $in['relpath']);

   /*
   print "<pre>";
   print_r($in);
   print_r($_SERVER);
   print "</pre>";
   */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
   <head>
      <title><?php print $in['path']; ?></title>
      <style>
         .icon {
            float: left;
            border: 1px solid #ccc;
            height: 75px;
            width: 75px;
            padding: 4px;
            margin: 1em;
            text-align: center;
         }
         A {
            text-align: center;
            color: #333;
            font-size: 11px;
            font-family: Optima,"Gill Sans","Gill Sans MT",sans-serif;
            text-decoration: none;
         }
         IMG.iconImage {
            margin-left: auto;
            margin-right: auto;
            border: 0px;
            margin-bottom: 4px;
         }
         
         div.icon img.iconImage {
            -mox-transition: all 1s;
            -webkit-transition: all 1s;
            transition: all 1s;
            width: 48px;
            height: 48px;
            margin-left:0px;
         }
         div.icon:hover {
            z-index:99999;
            position:relative;
         }
         div.icon:hover img {
            width:831px;
            height:340px;
            margin-left:-315px;
            z-index:99999;
            position:relative;
         }

         @keyframes expand {
            from { max-width:48px;max-height:48px; } 
            to { max-width:480px;max-height:none; }
         }
         @keyframes unexpand {
            from { max-width:480px;max-height:none; } 
            to { max-width:48px;max-height:48px; }
         }
      </style>
   </head>
   <body>
<?php
   if (is_dir($in['path'])) {
      $dh = opendir($in['path']);
      while ($file = readdir($dh)) {
         $icon = '';
         if ((!preg_match("/^\./", $file)) && (!preg_match("/CVS|\.bak|\.old|\.sw?/", $file))) {
            if (preg_match("/(jpg|png|gif)$/", $file)) {
               $imgs[] = $in['path'].'/'.$file;
               $parts = preg_split("/\./", $file);
               $ext = array_pop($parts);
               $thumbFile = join('.', $parts).'.png';
               $icon = $in['relpath'] . $file;
               $href = $in['relpath'] . $file;
               $tgt = "target='_blank'";
            } else if (is_dir($in['path'].'/'.$file)) {
               $icon = 'http://net2-dev.netoasis.net/img/aqua_folder_48.png'; 
               $href = preg_replace("|//|", '/', 'img.php?path=' . $in['relpath'] . '/' . $file);
               $tgt = '';
            } else {
               $mime = preg_replace("/\//", '-', mime_content_type($in['path'].'/'.$file));
               $icon = (file_exists("/home/cdr/net2/img/mimetypes/$mime.png")) ? "http://net2-dev.netoasis.net/img/mimetypes/$mime.png" : 'http://net2-dev.netoasis.net/img/mimetypes/text.png';
            }
            if ($icon) print "<div class='icon'><a $tgt href='$href' class='iconLink'><img src='$icon' class='iconImage' title='$file' /><br/>$file</div>\n";
         }
      }
      closedir($dh);
      if (count($imgs)) { 
         $util->checkThumbs($imgs, $in['path'].'/.thumbs');
      }
   }
?>
   </body>
</html>


<?php

class utility {
   function checkThumbs($arr, $path, $type='png') {
      if (!file_exists($path)) mkdir($path, 0775);

      $spath = preg_replace("/\//", '\\/', $_SERVER['DOCUMENT_ROOT']);
      $webpath = preg_replace("/$spath/", '', $path);

      foreach ($arr as $idx=>$file) {
         $paths = preg_split("/\//", $file);
         $tmp = array_pop($paths);
         $tmp2 = preg_split("/\./", $tmp);
         $ext = array_pop($tmp2);
         $fn = join('.', $tmp2);
         $fn .= '.' . $type;

         if (!file_exists($path.'/'.$fn)) {
            $this->makeThumb($file, $path.'/'.$fn, 48, 48, 80);
         }
         $thumbs[$idx] = $webpath . '/' . $fn;
      }
      return $thumbs;
   }

   function makeThumb($src, $dest, $width, $height, $quality) {
      $size = getimagesize($src, $info);
      
      $types = array('', 'gif', 'jpeg', 'png');

      $type = $types[$size[2]];
      if ($type) {
         if (($type == 'jpeg' || $type='jpg') && (!preg_match("/\.png$/", $src))) {
            $source = imagecreatefromjpeg($src);
         } else if ($type=='gif') {
            $source = imagecreatefromgif($src);
         } else if (($type='png') || preg_match("/\.png$/", $src)) {
            $source = imagecreatefrompng($src);
         }
         
         if (($size[0] > $width) || ($size[1] > $height)) {
               
            if ($width && ($size[0] > $width) && ($size[0] > $size[1])) {
               $r = ($width / $size[0]);
               $width  = $r * $size[0];
               $height = $r * $size[1];
            } else if ($height && ($size[1] > $height) && ($size[1] > $size[0])) {
               $r = ($height / $size[1]);
               $width  = $r * $size[0];
               $height = $r * $size[1];
            } else {
               $width = $size[0];
               $height = $size[1];
            }
            
            if ($width && $height) {
               $thumb = $this->imagecreatetransparent($width, $height);
            }
            
            if ($thumb) {
               imagecopyresampled($thumb, $source, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
               imagepng($thumb, $dest, $quality);
            }
         } else {
            copy($src, $dest);
         }
      }
      return true;
   }

   function imagecreatetransparent($x, $y) {
      $out   = @imagecreatetruecolor($x, $y);
      if ($out) {
         imagesavealpha($out, true);
         imagealphablending($out, false);
         $tlo = imagecolorallocatealpha($out, 220, 220, 220, 127);
         imagefill($out, 0, 0, $tlo);
      }
      
      return $out;
   }
}
?>
