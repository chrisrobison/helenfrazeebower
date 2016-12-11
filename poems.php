<?php
 $link = mysql_connect('localhost', 'mail', 'activate')
    or die('Could not connect: ' . mysql_error());
    mysql_select_db('hfb') or die('Could not select database');

$result = mysql_query("select * from poem");

while ($poem = mysql_fetch_assoc($result)) {
   $cr = mysql_query("select count(*) from comment where poem_id=".$poem['id']);
   $cmts = mysql_fetch_array($cr);
   $poem['comments'] = $cmts[0];
   $poems[] = $poem;
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title></title>
	<link rel="stylesheet" type="text/css" href="poem.css" />
	<link href="https://fonts.googleapis.com/css?family=IM+Fell+English" rel="stylesheet">
   <script src="poetry.js"></script>
</head>

<body>
   <div id="list">

   </div>
	<div id="poem">
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
      <?php

      ?>
	</div>
<script>
      function init() {
//         poemList();

         for (var i=0;i<poems.length;i++) {
            showPoem(i);
         }
      }

      function poemList() {
         var out="<ul>", txt, cnt=poems.length;
         for (var i=0;i<cnt;i++) {
            //txt = "<li><a href='#" + poems[i].title + "' onclick=\"return showPoem(" + i + ")\">"+poems[i].title+"</a></li>";
            txt = "<li><a href='#" + poems[i].title + "'>"+poems[i].title+"</a></li>";
            out += txt;
         }
         out += "</ul>";
         document.getElementById('list').innerHTML = out;
      }
      
      function showPoem(idx) {
         var poem = poems[idx],
             out = "<div class='pagebreak'></div>\n<div class='page'><h1 id='"+poem.title+"'>" + poem.title;

         // out += (poem.periodical) ? "<sup>*</sup>" : "";
         out += "</h1>";
         //out += "<h2>By Helen Frazee Bower</h2>";
         
         if (poem.verse) {
            poem.verse = poem.verse.replace(/^"/,'');
            var parts = poem.verse.split(/\"\s\-/,2);
            out += "<section class='verse'><q>" + parts[0] + "</q><br><cite>"+parts[1]+"</cite></section>\n<div class='flourish'><span></span></div>";
         }
         var style = "flush";

         var lines = poem.poem.replace(/\n\n/g,"\n").split(/\n/);
         if (lines[2][0].match(/\s/)) {
            style = "staggered";
         }
         if (poem.poem.match(/CHORUS|REFRAIN/i)) {
            style = "song";
         }
         if(poem.lines===14) {
            style = "staggered trailing";
         }

 
         var parts = poem.poem.split(/\n\n/);
         out += "<article class='poem "+style+"'>";
         
         for (var i=0;i<parts.length;i++) {
            var lines = parts[i].split(/\n/);
            out += "\n<ul class='stanza'>\n\t";
            for (var j=0;j<lines.length;j++) {
               if (lines[j].match(/\w/)) {
                  out += "\t<li>" + lines[j] + "</li>\n";
               }
            }
            out += "</ul>";
            // if (parts[i].match(/\w/)) { out += "<pre>" + parts[i] + "</pre>"; }
         }
         out += "</article>";
         // out += "<pre>" + poem.poem + "</pre>";
         if (poem['periodical']) {
            parts = poem.periodical.split(/\;/);

            out += "<h3 class='published'><sup>*</sup> As seen in <q>" + parts[0] + " Magazine</q>" + (parts[1] ? "; " + parts[1] : "") + "</h3>";
         }
         out += "</div>";
         document.getElementById("poem").innerHTML += out;
         return false;
      }
      init();
</script>
</body>
</html>
