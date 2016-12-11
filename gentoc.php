<?php
   print '<?xml version="1.0" encoding="UTF-8"?>\n';
?>
<!DOCTYPE ncx PUBLIC "-//NISO//DTD ncx 2005-1//EN"
"http://www.daisy.org/z3986/2005/ncx-2005-1.dtd">
 
<ncx version="2005-1" xml:lang="en" xmlns="http://www.daisy.org/z3986/2005/ncx/">
 
  <head>
<!-- The following four metadata items are required for all NCX documents,
including those conforming to the relaxed constraints of OPS 2.0 -->
 
    <meta name="dtb:uid" content="123456789X"/> <!-- same as in .opf -->
    <meta name="dtb:depth" content="1"/> <!-- 1 or higher -->
    <meta name="dtb:totalPageCount" content="0"/> <!-- must be 0 -->
    <meta name="dtb:maxPageNumber" content="0"/> <!-- must be 0 -->
  </head>
 
  <docTitle>
    <text>A Lifetime in Verse: The Sonnets &amp; Poetry of Helen Frazee-Bower</text>
  </docTitle>
 
  <docAuthor>
    <text>Robison, Christopher</text>
  </docAuthor>
<?php

$link = mysql_connect('localhost', 'mail', 'activate') or die('Could not connect: ' . mysql_error());
mysql_select_db('hfb') or die('Could not select database');

function makeNCX() {
   $poems = getPoems();
   $tochtml = "";
   $section = 1;
   $toc = array();
   $playorder = 3;
   $chapter = 1;
   $numbers = array('Zero', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven','Eight','Nine','Ten');
   $sectitle = "sonnet";
   $styles = array("sonnet"=>"The Sonnets","shortline"=>"The Short Ones", "song"=>"Songs &amp; Hymns", ""=>"The Rest");
   $secorder = 2;
   while ($poem = array_shift($poems)) {
      $toc[] = "<navPoint class=\"chapter\" id=\"level2-book{$section}chap{$chapter}\" playOrder=\"{$playorder}\">\n\t\t\t".
               "<navLabel><text>{$poem['title']}</text></navLabel>\n\t\t\t".
               "<content src=\"book.html#poem_{$poem['id']}\" />\n\t\t</navPoint>\n";
      if ($sectitle != $poem['style']) {
         $tochtml .= "\t<navPoint class=\"book\" id=\"level1-book{$section}\" playOrder=\"{$secorder}\">\n\t\t" .
                     "<navLabel><text>Part {$numbers[$section]}: {$styles[$sectitle]}</text></navLabel>\n\t\t<content src=\"book.html#section_{$section}\" />\n\t\t".
                     implode("\n\t\t", $toc)."\n\t</navPoint>\n";
         $toc = array();
         ++$section;
         $sectitle = $poem['style'];
         $secorder = $playorder;
         $playorder++;
      }
      ++$chapter;
      ++$playorder;
   }
   $tochtml .= "\t<navPoint class=\"book\" id=\"level1-book{$section}\" playOrder=\"{$secorder}\">\n\t\t" .
               "<navLabel><text>Part {$numbers[$section]}: {$styles[""]}</text></navLabel>\n\t\t<content src=\"book.html#section_{$section}\" />\n\t\t".
               implode("\n\t\t", $toc)."\n\t</navPoint>\n";

   return $tochtml;
}

function getPoems() {
   if ($cached) {
      return $cached;
   }
   $result = mysql_query("select * from poem order by style desc");
   
   while ($poem = mysql_fetch_assoc($result)) {
      $poems[] = $poem;
   }
   if (!$cached) {
      $cached = $poems;
   }
   return $poems;
}
?>
<navMap>
   <navPoint class="titlepage" id="L1T" playOrder="1">
      <navLabel><text>AUTHOR'S NOTE</text></navLabel>
      <content src="book.html#preface_1" />
   </navPoint> 
<?php 
   print makeNCX();
?>
</navMap>
</ncx>
