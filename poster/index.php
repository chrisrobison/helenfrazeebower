<?php
$in = $_REQUEST;

if (!$in['id']) {
   $in['id'] = rand(1,278);
}

$link = mysql_connect('localhost', 'mail', 'activate') or die('Could not connect: ' . mysql_error()); mysql_select_db('hfb') or die('Could not select database');
$result = mysql_query("select * from poem where id='".mysql_real_escape_string($in['id'])."'");

$poem = mysql_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>HTML5 boilerplate – all you really need…</title>
   <style>
      @charset "utf-8";
      @import url(font-awesome.min.css);
      @import url("https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700|Raleway:400,800,900");
      @import url("https://fonts.googleapis.com/css?family=IM+Fell+English");
      
      html,body,div,span,applet,object,iframe, h1,h2,h3,h4,h5,h6,p,blockquote,pre, a,abbr,acronym,address,big,cite,code, del,dfn,em,img,ins,kbd,q,s,samp, small,strike,strong,sub,sup,tt,var, b,u,i,center, dl,dt,dd,ol,ul,li, fieldset,form,label,legend, table,caption,tbody,tfoot,thead,tr,th,td, article,aside,canvas,details,figcaption,figure, footer,header,hgroup,menu,nav,section,summary, time,mark,audio,video{ margin:0; padding:0; border:0; outline:0; font-size:100%; font:inherit; vertical-align:baseline; }
      article,aside,details,figcaption,figure, footer,header,hgroup,menu,nav,section{ display:block; } 
      body{ line-height:1; font-family:"IM Fell English"; font-size:18px;}
      ol,ul{ list-style:none; }
      blockquote,q{ quotes:none; }
      blockquote:before,blockquote:after, q:before,q:after{ content:’’; content:none; }
      :focus{ outline:1px dotted #ccc; }
      ins{ text-decoration:none; }
      del{ text-decoration:line-through; }
      table{ border-collapse:collapse; border-spacing:0; }
      #poster {
         text-align:center;
      }
      #poem {
         margin:1em auto 1em auto;
         display:inline-block;
         padding: 1.5em 3em 2em;
         text-align:center;
         background-color: rgba(255,255,255,.5);
      }
      #poem span {
         text-align:left;
         white-space: pre;
         color: black;
         font-size: 1.3em;
         line-height: 2em;
         /*text-shadow: -1px -1px 0 #fff,  1px -1px 0 #fff, -1px 1px 0 #fff, 1px 1px 0 #fff;*/
       }
      #byline { 
         margin 1em auto 0 auto;
         font-size: 1.75em; 
         color: white;
         text-shadow:
            3px 3px 0 #000,
            -1px -1px 0 #000,  
            1px -1px 0 #000,
            -1px 1px 0 #000,
            1px 1px 0 #000;
            text-align:center;
  }
      #title { 
         margin: .5em auto; 
         font-size: 2.75em; 
         color: white;
         text-shadow:
            3px 3px 0 #000,
            -1px -1px 0 #000,  
            1px -1px 0 #000,
            -1px 1px 0 #000,
            1px 1px 0 #000;
            text-align:center;
            
      }
      #bgimage { object-fit:cover; position:absolute; height:1065;width:1420;top:0px;left:0px;}
      #poster { 
         background-image: url(/site/images/<?php print $poem['image']; ?>); 
         background-repeat: no-repeat;
         background-size: cover;
         position:absolute;
         top:0px;left:0px;
         width:100%;
         font-size:1.5em;
         line-height:2em;
         padding-bottom:4em;
      }
   </style>	
   <!--[if IE]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
</head>

<body id="home">
<div id="poster">
   <h1 id='title'><?php print $poem['title']; ?></h1>
   <h2 id='byline'>Helen Frazee-Bower</h2>
   <div id='poem'>
      <span><?php 
         print $poem['poem']; 
      ?></span>
   </div>
</div>
</body>
</html>
