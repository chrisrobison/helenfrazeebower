<?php

$link = mysql_connect('localhost', 'mail', 'activate') or die('Could not connect: ' . mysql_error());
mysql_select_db('hfb') or die('Could not select database');
$styles = array("sonnet"=>"The Sonnets","shortline"=>"The Short Ones", "song"=>"Songs &amp; Hymns", ""=>"The Rest");

function makeTOC() {
   $poems = getPoems("order by style desc");
   $tochtml = "";
   $section = 1;
   $toc = array();
   $sectitle = "sonnet";
   $styles = array("sonnet"=>"The Sonnets","shortline"=>"The Short Ones", "song"=>"Songs &amp; Hymns", ""=>"The Rest");
   while ($poem = array_shift($poems)) {
      if ($sectitle != $poem['style']) {
         $tochtml .= "<li><a href='#section_{$section}'>Part {$section}: {$styles[$sectitle]}</a><ol>".implode("\n", $toc)."</ol></li>\n";
         $toc = array();
         ++$section;
         $sectitle = $poem['style'];
      }
      $toc[] = "<li><a href='#poem_{$poem['id']}'>{$poem['title']}</a></li>";
   }
   $tochtml .= "<li><a href='#section_{$section}'>Part {$section}: {$styles[$sectitle]}</a><ol>".implode("\n", $toc)."</ol></li>\n";
   return $tochtml;
}

function getPoems($extra) {
   if ($cached && !$extra) {
      return $cached;
   }
   $result = mysql_query("select * from poem $extra");
   
   while ($poem = mysql_fetch_assoc($result)) {
      $poems[] = $poem;
   }
   if (!$cached) {
      $cached = $poems;
   }
   return $poems;
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<title>A Lifetime in Verse: The Sonnets &amp; Poetry of Helen Frazee-Bower</title>
	<link href="https://fonts.googleapis.com/css?family=IM+Fell+English" rel="stylesheet">
   <style>
      body {
        background: #fff;
        color: #111;
        font: 16px "IM Fell English",Lora,"Palatino Linotype","Times New Roman",Times,serif;
      }
      div, h1, h2, p {
        margin: 0;
        padding: 0;
      }
      a {
         color:#000000;
         text-decoration:none;
      }
      a:visited {
         color: #999999;
         text-decoration:none;
      }
      a:hover {
         color:#00aa00;
      }
      article {
         width: 25em;
         margin: 2.5em auto 1em auto;
         clear: both;
         text-align: center;
      }
      .poem {
         white-space: pre;
         padding-left:0px;
         display:inline-block;
         text-align:left;
         margin: .5em auto;
      }
      h1,h2,h3 {
         text-align:center;
      }
      h2 {
         margin: 1em 0px; 
      }
      ol {
         list-style-type: none;
      }
      div.pagebreak { margin-top:1em; page-break-after:always; }
      cite {
         text-align:center;
         margin: 1em auto;
         display:inline-block;
         float:right;
      }
      header {
         border-top:1px solid #666;
         border-bottom:1px solid #666;
         color:#000;
         font-size:.8em;
         padding:1em 2em;
         margin:1em auto 2em auto;
         font-style: italic;
      }

   </style>
</head>
<body>
	<div id="poem">
      <a name='title'></a>
      <div id="title">
         <br><br><br><br><br><br>
         <h1>A Lifetime in Verse</h1>
         <h2>The Sonnets and Poetry of Helen Frazee Bower</h2>
         <br><br>
         <h3>By Helen Frazee Bower</h3>
         <h3>Edited By Christopher D. Robison</h3>
         <br><br><br><br>
      </div>
      <div class="pagebreak"></div>
      <a name="section_1"></a>
      <article class='titlepage'>
         <br><br><h1>Part 1</h1><br><h1>The Sonnets</h1>
      </article>
      <div class="pagebreak"></div>
      <?php
         $poems = getPoems("order by style desc");
         $count = 0;
         $part = 1;
         $section = 'sonnet';
         while ($poem = array_shift($poems)) {
            $out = "<a name='poem_{$poem['id']}'></a>\n<article id='poem_{$poem['id']}'>";
            $out .= "\t<h2>{$poem['title']}</h2>\n\t";
            // $out .= "<h3>By Helen Frazee-Bower</h3>\n\t";
            if ($poem['verse']!='') {
               $poem['verse'] = preg_replace("/^\"/", "&ldquo;", $poem['verse']);
               $out .= "<header>{$poem['verse']}</header>\n\t";
            }
            $out .= "<p class='poem'>{$poem['poem']}</p>\n";
            if ($poem['periodical']) {
               if ($poem['style']!="song") {
                  $out .= "\t<cite>As seen in {$poem['periodical']} Magazine</cite>\n";
               } else {
                  $out .= "\t<cite>Published in {$poem['periodical']}</cite>\n";
               }
            }
            $out .= "</article>\n<div class='pagebreak'></div>\n";
            print $out;
            if ($section != $poem['style']) {
               $part++;
               print "<a name='section_{$part}'></a>\n<article class='titlepage'><br><br><h1>Part {$part}</h1><hr style='width:15%;margin:2em auto'><h1>{$styles[$poem['style']]}</h1></article><div class='pagebreak'></div>\n";
               $section = $poem['style'];
            }
         }
      ?>
	</div>
</body>
</html>
