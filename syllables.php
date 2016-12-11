<style>
   body { font-family: "Helvetica Neue", Hevetica, Arial, sans-serif; padding:0; margin:0; }
   td { border: 1px solid #ccc; padding:.25em .5em; }
   .total { color:#eee; background-color:#000; font-size:1.2em; font-weight:bold; }
   .title { font-weight:bold;font-size:1.2em; }
</style>
<?php
$link = mysql_connect('localhost', 'mail', 'activate') or die('Could not connect: ' . mysql_error());
mysql_select_db('hfb') or die('Could not select database');

$result = mysql_query("select * from poem where style='sonnet'");
$gotfields = 0;

$vowels = array("a", "e", "i", "o", "u");
$consts = array("b","c","d","f","g","h","j","k","l","m","n","p","q","r","s","t","v","w","x","z");
$diphthongs = array("aa","ae","ai","ao","au","ea","ee","ei","eo","eu","ia","ie","ii","io","iu","oa","oe","oi","oo","ou","ua","ue","ui","uo","uu","are");
$vowel = "[aeiouy]";
$const = "([bcdfghjklmnpqrstvwxz]|ss|tt|ll|dd|gg|rr|pp|mm|cc)";
$exceptions = array("quiet"=>2, "beloved"=>3,"beauty"=>2,"radiance"=>3,"higher"=>2,"thanked"=>1,"heavens"=>2,"vague"=>1,"learned"=>1,"evening"=>1,"together"=>2,"needed"=>2,"garbed"=>1,"firelight"=>2,"faces"=>2,"every"=>2,"discouragement"=>4,"belong"=>1,"beyond"=>2,"climbed"=>1,"alien"=>3,"weary"=>1,"she"=>1,"poets"=>2,"weary"=>2,"blessed"=>2,"encouragement"=>4,"clenched"=>1,"radiant"=>3,"quietness"=>3,"crumbling"=>3, "careless"=>2,
   "silenced"=>2,"silences"=>3,"curved"=>1,"gaiety"=>3,"perched"=>1,"listening"=>2,"places"=>2,"tiresome"=>2,"another"=>2,"realize"=>3,"deepest"=>2,"memories"=>2,"violets"=>2,"enough"=>1,"quivers"=>2,"defeated"=>3,"ages"=>2,"peter"=>1,"cruel"=>2,"settling"=>3,"fragilely"=>2,"branches"=>2,"higher"=>2,"myriad"=>3,"burned"=>1,"turned"=>1,"walked"=>1,"inched"=>1,"worked"=>1,"everything"=>3,"asked"=>1,"aloft"=>1,"forever"=>3,"haunted"=>1,"reminding"=>2,"token"=>1,"lifetime"=>2,"enough"=>2,"burdened"=>2,"smothered"=>1,"regretting"=>2,"reaches"=>2,"another"=>3,"ruin"=>2,"passes"=>2,"edges"=>2,"wishes"=>2,"busy"=>1,"boundaries"=>2,"andrea"=>3,"reassuring"=>4,"compasses"=>3,"voices"=>2,"reality"=>4,"premises"=>3,"assurances"=>4,"sparkling"=>3,"cherishes"=>3,"together"=>3,"clouded"=>2,"creator"=>3,"throated"=>2,"circling"=>3,"triumph"=>2,"regenerated"=>5,"grasses"=>2,"fretted"=>2,"joyously"=>3,"struggles"=>2,"trembles"=>2,"joyous"=>2,"ritual"=>3,"repeated"=>3,"waited"=>2,"houses"=>2,"slippers"=>3,"ransomed"=>2,"touches"=>2,"learned"=>2,"shaded"=>2,"someone"=>2,"isn"=>2,"chaos"=>2,"gulches"=>2,"sombre"=>2,"obedience"=>4,"dogged"=>2,"seemed"=>2,"circles"=>2,"pages"=>2,"candles"=>2,"hour"=>2,"gethsemane"=>4,"pinnacles"=>3);
$diph = "(aa|ae|ai|ao|au|ea|ee|ei|eo|eu|ia|ie|ii|io|iu|oa|oe|oi|oo|ou|ua|ue|ui|uo|uu)";

function getSyllables($word) {
   global $vowel;
   global $const;
   global $exceptions;
   global $diph;

   $count = strlen(preg_replace("/[^X]/", "", preg_replace("/".$vowel."+/", "X", strtolower($word))));
   foreach ($diphthongs as $d) {
      if (preg_match("/".$d."/", $word)) {
         $count--;
      }
   }
   if (preg_match("/".$vowel."ing/", $word)) {
      $count++;
   }
   if ((strlen($word)>2) && ($word!="the") && (preg_match("/[bcdfghjklmnpqrstvwxz]es?$/", $word))) {
      $count--;
   }
   if (preg_match("/some\w+/", $word)) {
      $count--;
   }
   if (preg_match("/".$const."le$/", $word)) {
      $count++;
   }
   if (preg_match("/ely$/", $word)) {
      $count--;
   }
   if (preg_match("/".$diph.$const."e".$const."\w+/", $word)) {
      $count--;
   }
   if (preg_match("/ssed$/", $word)) {
   //   $count++;
   } 
   if (preg_match("/".$vowel.$const."ed$/", $word)) {
      $count--;
   }
   if (preg_match("/shed$/", $word)) {
      $count--;
   }
   if (preg_match("/ened$/", $word)) {
      $count++;
   }
   if (preg_match("/^".$const.$vowel."[ktd]ed$/", $word)) {
      $count++;
   }
   
   if ($exceptions[$word]) {
      $count = $exceptions[$word];
   }

   return $count;
}
$notten = 0;
while ($poem = mysql_fetch_assoc($result)) {
   if (!$gotfields) {
      $fields = array_keys($poem);
      $gotfields = 1;
      //print "\t" . implode("\n\t", $fields)."\n";
   }
   $lines = preg_split("/\n/", preg_replace("/\n\n/", "\n", html_entity_decode($poem['poem'])));
   if ($lines[0]=="") {
      array_shift($lines);
   }

   $count = count($lines);

   foreach ($lines as $idx=>$line) {
      $tally = array();
      $line = strtolower($line);
      $line = preg_replace("/o\'er/", "over", $line);
      for ($i=0; $i<strlen($line); $i++) {
         if (!$tally[$line[$i]]) {
            $tally[$line[$i]] = 0;
         } 
         $tally[$line[$i]]++;

         //if (($line[$i] == "e") && ($line[$i+1] == " ")) { $tally[$line[$i]]--; }
      }
      // print $line;
      $tot = 0;
      $words = preg_split("/[\W]/", trim($line));
      $out = "<hr><table style='border-collapse:collapse;width:20em;margin:1em auto;'><tr><td class='title' colspan='2'>{$poem['title']} [{$poem['id']}]</td></tr><tr><td colspan='2'>Lines: {$count}</td></tr><tr style='color:#ccc;background-color:#000;'><td colspan='3'>$line</td></tr>";
      foreach ($words as $word) {
         $word = trim($word);
         if ($word) {
            $cnt = getSyllables(preg_replace("/\W/", '', $word));
            $out .= "<tr><td style='width:50%;text-align:right;'>".$word.":</td><td style='width:50%'>".$cnt."</td></tr>\n";
            $tot += $cnt;
         }
      }

      $out .= "<tr class='total'><td style='text-align:right'>Total:</td><td>$tot</td></tr></table>\n";

      if ($tot<10) {
         print $out."\n";
         ++$notten;
      }
   }
   $totlines += $count;
}
print "$notten lines out of $totlines (".round(($notten/$totlines)*100)."%) do not have 10 syllables.\n";
//print "var poems = " . json_encode($poems) . ";\n";
?>

