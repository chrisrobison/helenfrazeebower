#!/usr/bin/php
<?php
$link = mysql_connect('localhost', 'mail', 'activate')
    or die('Could not connect: ' . mysql_error());
    mysql_select_db('hfb') or die('Could not select database');
   
    $nokeywords = array("is"=>1,"are"=>1,"am"=>1,"was"=>1,"were"=>1,"can"=>1,"could"=>1,"do"=>1,"did"=>1,"has"=>1,"have"=>1,"had"=>1,"will"=>1,"would"=>1,"may"=>1,"might"=>1,"must"=>1,"have"=>1,"to"=>1,"shall"=>1,"should"=>1,"be"=>1,"being"=>1,"been"=>1,"does"=>1,"the"=>1,"with"=>1,"not"=>1,"for"=>1,"when"=>1,"and"=>1,"where"=>1,"what"=>1,"why"=>1,"him"=>1,"too"=>1,"to"=>1,"comes"=>1,"though"=>1,"there"=>1,"all"=>1,"every"=>1,"his"=>1,"that"=>1,"this"=>1,"see"=>1,"our"=>1,"but"=>1,"than"=>1,"then"=>1,"still"=>1,"which"=>1,"just"=>1,"but"=>1,"dismiss"=>1,"still"=>1,"ways"=>1,"way"=>1,"you"=>1,"me"=>1,"make"=>1,"makes"=>1,"soft"=>1,"out"=>1,"your"=>1,"name"=>1,"first"=>1,"you"=>1,"they"=>1,"yet"=>1,"only"=>1,"like"=>1,"some"=>1,"say"=>1,"says"=>1,"who"=>1,"let"=>1,"once"=>1,"nor"=>1,"she"=>1,"he"=>1,"her"=>1,"hers"=>1,"his"=>1,"him"=>1,"went"=>1,"made"=>1,"known"=>1,"gave"=>1,"again"=>1,"now"=>1,"saw"=>1,"them"=>1,"gone"=>1,"lay"=>1,"from"=>1,"how"=>1,"into"=>1,"through"=>1,"call"=>1,"each"=>1,"into"=>1,"up"=>1,"down"=>1,"things"=>1,"thing"=>1,"one"=>1,"perhaps"=>1,"give"=>1,"tell"=>1,"ask"=>1,"very"=>1,"take"=>1,"know"=>1,"ldquo"=>1,"find"=>1,"until"=>1,"look"=>1,"looking"=>1);
   $txt = file_get_contents("poetry.txt");

   $poems = preg_split("/\f/s", $txt);
   
   $tmpout = array();
   foreach ($poems as $idx=>$val) {
      $lines = preg_split("/\n/s", $val);
      $key = preg_replace("/\s*\(continued\)/i", '', preg_replace("/^[\s\*]*/", '', $lines[2]));
      $key = preg_replace("/[\r\n]/s", '', $key);

      if (preg_match("/^\"/", $lines[4])) {
         $verses[$key] = trim($lines[4]);
         $t = array_splice($lines, 4, 2);
      }
      // print "Key: $key\n";
      $tmp = array_slice($lines,3);
      // print_r($tmp);
      // print $tmp[0]."\n";
      $tmpout[$key] .= implode("\n",$tmp);
   }
   $out = array();
   $other = array();
   
   $topWords = 0;
   $topLetters = 0;
   $leastWords = 100;
   $leastLetters = 100;
   $pcount = 0;
   foreach ($tmpout as $title=>$poem) {
      $item = array();
      
      if (preg_match("/\w/", $title)) {
         $item["title"] = $title;
         
         if ($verses[$title]) {
            $item["verse"] = $verses[$title];
         }

         if (preg_match("/\*\s*(.*)/", $poem, $matches)) {
            $item["periodical"] = trim($matches[1]);
            $poem = preg_replace("/\*\s*(.*)/", "", $poem);
         }
         $poem = preg_replace("/\r\n/s", "\n", $poem);
         $poem = preg_replace("/^\n/s", "", $poem);
         $poem = preg_replace("/^\s+$/s", "", $poem);
         $poem = preg_replace("/\n*$/s", "", $poem);

         $lines = preg_split("/\n/", preg_replace("/\n\n/", "\n", $poem));
         $lcount = 0;
         $wcount = 0;
         foreach ($lines as $key=>$line) {
            $lcount += strlen(preg_replace("/\s/","", $line));
            $words = explode(" ", $line);
            $wcount += count($words);
         }
         $item["firstline"] = $lines[0];
         $item["stats"] = array();
         $item["linecount"] = $item["stats"]["lines"] = count($lines);
         $item["wordcount"] = $item["stats"]["words"] = $wcount;
         $item["lettercount"] = $item["stats"]["characters"] = $lcount;
         $avgW = $item["stats"]["averageWordsPerLine"] = round($wcount/count($lines));
         $avgL = $item["stats"]["averageCharactersPerLine"] = round($lcount/count($lines));
         
         if ($pcount<303) {
            $item["image"] = "pic" . sprintf("%03d", $pcount) . ".jpg";
         } else {
            $item["image"] = "pic" . sprintf("%03d", $pcount - 303) . ".jpg";
         }
         
         $item["poem"] = $poem;

         if (preg_match_all("/\b(\w+)\b/", $poem, $matches)) {
            $mc = count($matches[1]);
            $keywords = array();
            for ($i=0; $i<$mc; $i++) {
               $m = strtolower($matches[1][$i]);
               if ((strlen($m) > 2) && (!$nokeywords[$m])) {
                  if (!$keywords[$m]) {
                     $keywords[$m] = 1;
                  } else {
                     $keywords[$m]++;
                  }
               }
            }
            arsort($keywords);
            $keys = array_slice(array_keys($keywords), 0, 10, true);

            $item["keywords"] = implode(",", $keys);
         }
         $item['id'] = $pcount;
         if (($item["periodical"]) && ($avgW<6)) {
            $shortLined[] = $item;
         } else if ($item["periodical"]) {
            $out[] = $item;
         } else if ($avgW<6) {
            $item["style"] = "shortline";
            $shortLined[] = $item;
         } else {
            $other[] = $item;
         }
      }
      $fields = array();
      $vals = array();

      $insert = "insert into poem (";
      foreach ($item as $key=>$val) {
         if ($key!="stats") {
            $fields[] = $key;
            $vals[] = "'".mysql_real_escape_string($val)."'";
         }
      }

      $insert .= implode($fields, ",") . ",created) VALUES (" . implode($vals,",") . ",now())";
      
      if (count($fields)) {
         print $insert.";\n";
      }
//      mysql_query($insert);

      ++$pcount;
   }
   //shuffle($out);
   //shuffle($other);

   $out = array_merge($out, $shortLined);
   $out = array_merge($out, $other);
//   print "var poems = " . json_encode($out) .";\n";
//   print "var shortLinedPoems = " . json_encode($shortLined) . ";\n";

?>
