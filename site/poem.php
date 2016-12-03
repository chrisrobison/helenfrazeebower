<?php
$in = $_REQUEST;

if (!$in['id']) {
   $in['id'] = rand(1,270);
}

$out = "";
foreach ($_SERVER as $key=>$val) {
   $out .= "$key: $val\n";
}

file_put_contents("/tmp/ua.log", $out."====\n", FILE_APPEND);
if (!preg_match("/facebook/i", $_SERVER['HTTP_USER_AGENT'])) {
   header("Location: http://helenfrazeebower.com/site/#".$in['id']);
   exit;
}

$link = mysql_connect('localhost', 'mail', 'activate')
    or die('Could not connect: ' . mysql_error());
    mysql_select_db('hfb') or die('Could not select database');

$result = mysql_query("select * from poem where id=\"" . mysql_real_escape_string($in['id']) . "\" LIMIT 1");

$poem = mysql_fetch_assoc($result);
print "<meta property=\"fb:app_id\" content=\"1707007269614439\" />\n";
print "<meta property=\"og:title\" content=\"{$poem['title']}\" />\n";
print "<meta property=\"og:description\" content=\"".preg_replace("/\n/s", "\\n / ", $poem['poem'] )."\" />\n";
print "<meta property=\"og:type\" content=\"website\" />\n";
print "<meta property=\"og:url\" content=\"http://helenfrazeebower.com/site/poem.php?id={$poem['id']}\" />\n";
print "<meta property=\"og:image\" content=\"http://helenfrazeebower.com/site/images/{$poem['image']}\" />\n";
print "<meta property=\"og:image:width\" content=\"1420\" />\n";
print "<meta property=\"og:image:height\" content=\"1065\" />\n";
print "<html><head><title>{$poem['poem']}</title></head><body>\n";

print "<img src='images/{$poem['image']}'><br>\n";
print "<h1>{$poem['title']}</h1>\n";
print "<h2>By Helen Frazee-Bower</h2>\n";
print "<pre>\n";
print $poem['poem']."\n";
print "</pre><br>\n";

print "<a href='http://helenfrazeebower.com/site/#".$in['id']."'>".$poem['title']."</a>";
print "</body></html>";
?>
