var boss = (function() {
   var tpl = document.querySelector("#post-template");
   var minitpl = document.querySelector("#mini-post");
   var tinytpl = document.querySelector("#tiny-post");
   var main = document.getElementById("content");
   var tiny = document.getElementById("tiny-posts");
   var navbuttonDOM = document.getElementById("navbuttons");
   var navbutton = "<ul id='navbuttons' class='actions pagination'>" + navbuttonDOM.innerHTML + "</ul>";

   return {
      getPoemById: function(id) {
         for (var i=0; i<poems.length; i++) {
            if (poems[i].id == id) {
               return poems[i];
            }
         }
         return poems[0];
      },
      getPoemByTitle: function(title) {
         for (var i=0; i<poems.length; i++) {
            if (poems[i].title == title) {
               return poems[i];
            }
         }
         return false;
      },
      getImage: function() {
            var img = "images/pic";
            var r = Math.round(Math.random()*303);
            if (r<100) {
               if (r<10) {
                  img += "00" + r + ".jpg";
               } else {
                  img += "0" + r + ".jpg";
               }
            } else {
               img += r + ".jpg";
            }
         return img; 
      },
      makeArticle: function(id, clear) {
         var obj = boss.getPoemById(id);
         var clone = document.importNode(tpl.content, true);
         obj["image"] = (!obj["image"]) ? boss.getImage() : obj["image"];
         console.log("makeArticle: "+id);
         console.dir(obj);

         var txt = tpl.innerHTML;
         var fill = function(str, p1, offset, s) { return obj[p1]; };
         
         obj["formattedPoem"] = boss.formatPoem(obj);
         txt = txt.replace(/\{\{(.+?)\}\}/g, fill);
         
         var el = document.createElement("article");
         el.className = "poem";

         var navbuttonDOM = document.getElementById("navbuttons");
         el.innerHTML = txt;
         if (clear) {
            main.innerHTML = "";
         } 
         main.appendChild(el);
         if (window.FB) {
            FB.XFBML.parse();
         }
         window.scroll({top:0,left:0,behavior:'smooth'});
      },
      formatPoem: function(obj) {
         var out = "<h1>" + obj.title;
         out += "</h1>";
         out += "<h2>By Helen Frazee Bower</h2>";
         var style = "flush";

         var lines = obj.poem.replace(/\n\n/g,"\n").split(/\n/);
         if (lines[2][0].match(/\s/)) {
            style = "staggered";
         }
         if (obj.poem.match(/CHORUS|REFRAIN/i)) {
            style = "song";
         }
         if(obj.linecount==14) {
            style = "staggered trailing";
         }

          if (obj.verse) {
            obj.verse = obj.verse.replace(/^"/,'');
            var parts = obj.verse.split(/\"\s[\—\-]/,2);
            parts[1] = parts[1] ? parts[1] : "";
            out += "<figure class='verse'><blockquote class='verse'>" + parts[0] + "</blockquote><figcaption>"+parts[1]+"</figcaption></figure><br>\n";
         }
         out += "<div class='content " + style + "'>";
         var parts = obj.poem.split(/\n\n/);
         
         for (var i=0;i<parts.length;i++) {
            var lines = parts[i].split(/\n/);
            if ((lines.length>1) || ((lines.length==1) && (lines[0] !=""))) {
               out += "\n<ul class='stanza'>\n\t";
               for (var j=0;j<lines.length;j++) {
                  if (lines[j].match(/\w/)) {
                     out += "\t<li>" + lines[j] + "</li>\n";
                  }
               }
            }
            out += "</ul>";
         }
         out += "</div>";
         if (obj['periodical']) {
            parts = obj.periodical.split(/\;/);

            out += "<cite><sup>*</sup> As seen in <q>" + parts[0] + " Magazine</q>" + (parts[1] ? "; " + parts[1] : "") + "</cite>";
         }
         return out;
      },
      getFirstLine(content) {
         var lines = content.replace(/^\n*/g,'').split(/\n/);
         return lines[0];
      },
      getFirstStanza(content) {
         var parts = content.split(/\n\n/);
         return parts[0];
      },
      makeMiniPost: function(idx) {
         var txt = minitpl.innerHTML,
             obj = shortLinedPoems[idx];
         obj.poem = obj.poem.replace(/^\n/g,'');
         obj["firstStanza"] = boss.getFirstStanza(obj.poem);
         obj["image"] = (!obj["image"]) ? boss.getImage() : obj["image"];
         
         var fill = function(str, p1, offset, s) { return obj[p1]; };
         txt = txt.replace(/\{\{(.+?)\}\}/g, fill);
         
         var el = document.createElement("li");
         el.innerHTML = txt;
         
         var mini = document.getElementById("mini-posts");
         mini.appendChild(el);
      },
      makeTinyPost : function(idx) {
         var txt = tinytpl.innerHTML,
             obj = poems[idx];

         obj["image"] = (!obj["image"]) ? boss.getImage() : obj["image"];
         
         var fill = function(str, p1, offset, s) { return obj[p1]; };
         txt = txt.replace(/\{\{(.+?)\}\}/g, fill);
         
         var el = document.createElement("li");
         
         el.innerHTML = txt;
         tiny.appendChild(el);
      },
      sortByTitle: function(arr) {
         var sorted = arr.sort(function(a, b) {
            if (a.title < b.title) {
               return -1;
            } 
            if (a.title > b.title) {
               return 1;
            }
            return 0;
         });
         return sorted; 
      },
      doAction: function(action) {
         
         if ((action === "next") && (boss.currentPoemId)) {
            boss.currentPoemId++;
            if (boss.currentPoemId > poems.length) {
               boss.currentPoemId = 1;
            }
            boss.makeArticle(boss.currentPoemId, true);
            boss.setNav(boss.currentPoemId);
            window.scroll({top:0,left:0,behavior:'smooth'});
            return false;
         } else if ((action === "next") && (boss.currentPoemId==undefined)) {
            boss.makeSet();
            window.scroll({top:0,left:0,behavior:'smooth'});
            return false;
         } else if ((action === "prev") && (boss.currentPoemId)) {
            boss.currentPoemId--;
            if (boss.currentPoemId < 0) {
               boss.currentPoemId = poems.length - 1;
            }
            boss.makeArticle(boss.currentPoemId, true);
            boss.setNav(boss.currentPoemId);
            window.scroll({top:0,left:0,behavior:'smooth'});
            return false;
         } else if ((action === "prev") && (boss.currentPoemId==undefined)) {
            boss.makeSet();
            window.scroll({top:0,left:0,behavior:'smooth'});
            return false;
         } else if (action === "about") {
            document.getElementById("about").scrollIntoView({behavior:"smooth",block:"end"});
         } else if (action === "tiny-posts") {
            document.getElementById("tiny-posts").scrollIntoView({behavior:"smooth",block:"end"});
         } else {
            console.log("Should be handling action: "+action);
         }

      },
      makeSet: function() {
         var oldpoems = document.querySelectorAll("#main article.poem");

         for (var i=0; i< oldpoems.length; ++i) {
            oldpoems[i].parentNode.removeChild(oldpoems[i]);
         }

         var clr = false;
         for (var i=0; i<5; i++) {
            var idx=Math.round(Math.random()*poems.length-1) + 1;
            boss.makeArticle(idx,clr);
            clr = false;
         }
      },
      like: function(el) {
         var likes = parseInt(el.innerHTML);
         likes++;
         el.innerHTML = likes;
         
         var poem = boss.getPoemById(boss.currentPoemId);
         poem.likes++;
         boss.handleClick(el);
         return false;
      },
      handleClick: function(el) {
         var qs = el.href.replace(/.*\?/,'');
         var items = qs.split(/\&/g);
         var out = {};
         for (var i=0; i<items.length; i++) {
            var parts = items[i].split(/=/, 2);
            out[parts[0]] = parts[1];
         }
         boss.exec(el.href, out, function(data) {
            console.dir(data);
         }, function(status) {
            console.log("Error: " + status);
         });
         return false;
      },
      toggleComments: function(id) {
         console.log("toggleComments: "+id);
         var el = document.getElementById("comments"+id);
         if (el) {
            el.classList.toggle("closed");
         }
         return false;
      },
      exec: function(url, data, success, error) {
         var xhr = typeof XMLHttpRequest != 'undefined' ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
         xhr.open('POST', url, true);
         xhr.responseType = 'json';
         xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
         xhr.onreadystatechange = function() {
            var status;
            // https://xhr.spec.whatwg.org/#dom-xmlhttprequest-readystate
            if (xhr.readyState == 4) { // `DONE`
               status = xhr.status;
               if (status == 200) {
                  success && success(xhr.response);
               } else {
                  error && error(status);
               }
            }
         };
         xhr.send(JSON.stringify(data));
      },
      setNav: function(id) {
         if (id==1) {
            document.getElementById("btnPrev").style.display = "none";
         } else {
            document.getElementById("btnPrev").style.display = "inline-block";
         }

         if (id==poems.length-1) {
            document.getElementById("btnNext").style.display = "none";
         } else {
            document.getElementById("btnNext").style.display = "inline-block";
         }
      
      },
      fb_showFace: function() {
         var obj = boss.user;
         boss.user = obj;
         if (obj.picture && obj.picture.data && obj.picture.data.url) {
            var fbpicEl = document.getElementById("fbpic");
            var fbpic = document.createElement("img");
            fbpic.style.margin = "2px";
            fbpic.src = obj.picture.data.url;
            fbpicEl.innerHTML = "";
            fbpicEl.appendChild(fbpic);
         }
      },
      fb_statusChangeCallback: function(response) {
          if (response.status === 'connected') {
            boss.fb_login();
          }
      },
      fb_checkLoginState: function() {
          FB.getLoginStatus(function(response) {
            boss.fb_statusChangeCallback(response);
          });
      },
      fb_login: function() {
         FB.api('/me?fields=id,first_name,last_name,email,picture', function(response) {
            boss.user = response;
            console.log('Successful login for: ' + response.name);
            console.dir(response);
            boss.fb_showFace();
         });
      }, 
      init: function() {
         //poems = boss.sortByTitle(poems);
         //shortLinedPoems = boss.sortByTitle(shortLinedPoems);
         boss.navbuttons = "<ul id='navbuttons' class='actions pagination'>" + navbuttonDOM.innerHTML + "</ul>";
         
         for (var i=0; i<5; i++) {
            boss.makeMiniPost(i);
         }
         for (var i=0; i<poems.length; i++) {
            boss.makeTinyPost(i);
         }

         if (document.location.hash) {
            var id = document.location.hash.replace(/^#/, '');

            if ((id=="next") || (id=="prev")) {
               id = boss.currentPoemId || 1;
            }
            boss.makeArticle(id);
            boss.currentPoemId = id;
            boss.setNav(id);
         } else {
            boss.makeSet();
         }

         var links = document.querySelectorAll("a");
         links.forEach (function(el, i) {
            el.addEventListener("click", function(e) {
               var action = this.hash.replace(/#/,'');
               if (action.match(/^\d+$/)) {
                  boss.currentPoemId = action;
                  boss.makeArticle(action, true);
                  window.scroll({top:0,left:0,behavior:'smooth'});
               } else if (action) {
                  boss.doAction(action);
               }
            });
         });
         if (window.FB) {
            FB.getLoginStatus(function(response) {
               if (response.status==="connected") {
                  boss.fb_showFace();
               }
            });
         }
      }
   };
})();

window.fbAsyncInit = function() {
   FB.init({
      appId      : '1707007269614439',
      cookie     : true,  
      xfbml      : true, 
      version    : 'v2.8'
   });

   FB.getLoginStatus(function(response) {
      boss.fb_statusChangeCallback(response);
   });
};

