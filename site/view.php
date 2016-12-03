<?php
 $link = mysql_connect('localhost', 'mail', 'activate')
    or die('Could not connect: ' . mysql_error());
    mysql_select_db('hfb') or die('Could not select database');

$result = mysql_query("select * from poem");

while ($poem = mysql_fetch_assoc($result)) {
   print "<img src='{$poem['image']}' style='float:left;max-width:300px;margin-top:2px;'><h2>{$poem['title']} ({$poem['image']})</h2><br clear='both'>\n";
   $poems[] = $poem;
}


