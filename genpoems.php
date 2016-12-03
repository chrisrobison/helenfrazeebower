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

print "var poems = " . json_encode($poems) . ";\n";

$result = mysql_query("select * from poem where linecount < 9 order by wordcount limit 5");

while ($poem = mysql_fetch_assoc($result)) {
   $short[] = $poem;
}

print "var shortLinedPoems = " . json_encode($short) . ";\n";
?>
