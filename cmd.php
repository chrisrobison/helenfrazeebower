<?php

$db = mysql_connect('localhost', 'mail', 'activate') or die('Could not connect: ' . mysql_error());
mysql_select_db('hfb') or die('Could not select database');

$in = $_REQUEST;
if ($in['id']) {
   $poem = get($in['id']);
}

if ($in['x']) {
   switch ($in['x']) {
      case "like":
         $out = like($poem); 
         break;
      case "comment":
         $out = comment($in);
         break;
      case "getComments":
         $out = getComments($in['id']);
         break;
      case "update":
         $out = update($in, $poem);
         break;
      case "get":
      default:
         $out = get($in['id']);
   }

   print json_encode($out);
}


function update($in, $poem) {
   $upd = array();
   foreach ($poem as $key=>$val) {
      if ($in[$key] != $val) {
         $upd[] = $key . "='".mysql_real_escape_string($in[$key])."'";
      }
   }
   $update = "update poem set " . implode(",", $upd) . " where id={$poem['id']}";
   mysql_query($update);

   $out["success"] = 1;
   $out["action"] = "update";
   $out["query"] = $update;

   return $out;
}

function like($poem) {
   $poem['likes']++;
   $upd = "update poem set likes=likes+1 where id={$poem['id']}";
   mysql_query($upd);

   $out["success"] = 1;
   $out["action"] = "like";
   $out["query"] = $upd;

   return $out;
}

function get($id) {
   $query = "select * from poem";
   if ($id) {
      $query .= " where id=" . $id;
   }
   $result = mysql_query($query);

   while ($poem = mysql_fetch_assoc($result)) {
      $poems[] = $poem;
   }

   return (count($poems)==1) ? $poems[0] : $poems;
}

function comment($in) {
   $ins = "insert into comment (id, poem_id, commenter, comment, created, lastModified) values (null, {$in['id']}, '".mysql_real_escape_string($in['email'])."', '".mysql_real_escape_string($in['comment'])."',now(),now())";
   $result = mysql_query($ins);
   
   $out["success"] = 1;
   $out["query"] = $ins;
   $out["action"] = "comment";

   return $out;
}

function getComments($id) {
   $query = "select * from comment where poem_id=" . $id;
   $result = mysql_query($query);

   while ($comment = mysql_fetch_assoc($result)) {
      $comments[] = $comment;
   }

   return $comments;
}

function getComment($comment_id) {
   $query = "select * from comment where id=" . $comment_id;
   $result = mysql_query($query);

   $comment = mysql_fetch_assoc($result);

   return $comment;
}
?>
