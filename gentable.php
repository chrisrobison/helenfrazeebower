#!/usr/bin/php
<?php
 $link = mysql_connect('localhost', 'mail', 'activate')
    or die('Could not connect: ' . mysql_error());
    mysql_select_db('hfb') or die('Could not select database');

$result = mysql_query("select * from poem");
$gotfields = 0;

while ($poem = mysql_fetch_assoc($result)) {
   if (!$gotfields) {
      $fields = array_keys($poem);
      $gotfields = 1;
      print implode("|", $fields)."\n";
   }
   $poem['poem'] = preg_replace("/\n/", "\\n", $poem['poem']);
   print implode("|", array_values($poem))."\n";
   $poems[] = $poem;
}

//print "var poems = " . json_encode($poems) . ";\n";
?>

