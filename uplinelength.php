<?php
$link = mysql_connect('localhost', 'mail', 'activate')
    or die('Could not connect: ' . mysql_error());
    mysql_select_db('hfb') or die('Could not select database');

$result = mysql_query("select * from poem");

while ($poem = mysql_fetch_assoc($result)) {
   $lines = preg_split("/\n/", preg_replace("/\n\n/", "\n", $poem['poem']));
   $longest = 0;
   $shortest = 1000;
   $count = 0;
   foreach ($lines as $str) {
      $len = strlen($str);
      $count += $len;
      if ($len > $longest) {
         $longest = $len;
      }
      if ($len < $shortest) {
         $shortest = $len;
      }
   }
   $avg = round($count/count($lines));

   print "Poem: {$poem['title']}\nLines:".count($lines)."\nAverage line length: {$avg}\nShortest line: {$shortest}\nLongest line: {$longest}\n\n";
}
?>
